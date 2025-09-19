<?php

/**
 * Author: Eng.Fahed
 * Leave Seeder - HR System
 * إنشاء إجازات واقعية لكل موظف مع تعليقات وحالات مختلفة
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Leave;
use App\Models\LeaveComment;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏖️ Creating leave requests for employees...');

        $employees = Employee::where('employment_status', 'active')->take(50)->get();
        $totalLeaves = 0;
        $totalComments = 0;

        foreach ($employees as $employee) {
            $leaveCount = rand(2, 5);
            
            for ($i = 1; $i <= $leaveCount; $i++) {
                $leave = $this->createLeaveForEmployee($employee);
                $totalLeaves++;
                
                $commentsCreated = $this->createCommentsForLeave($leave);
                $totalComments += $commentsCreated;
            }
        }

        $this->command->info("✅ Created {$totalLeaves} leave requests with {$totalComments} comments");
    }

    private function createLeaveForEmployee(Employee $employee): Leave
    {
        $leaveTypes = ['annual', 'sick', 'emergency', 'study'];
        $leaveType = $leaveTypes[array_rand($leaveTypes)];
        
        $durations = ['annual' => [3, 7, 14], 'sick' => [1, 2, 3], 'emergency' => [1, 2], 'study' => [1, 2]];
        $duration = $durations[$leaveType][array_rand($durations[$leaveType])];
        
        $timeFrames = ['past', 'current', 'future'];
        $timeFrame = $timeFrames[array_rand($timeFrames)];
        
        switch ($timeFrame) {
            case 'past':
                $startDate = Carbon::now()->subDays(rand(30, 180));
                $status = ['approved', 'rejected'][array_rand(['approved', 'rejected'])];
                break;
            case 'current':
                $startDate = Carbon::now()->subDays(rand(1, $duration));
                $status = 'approved';
                break;
            case 'future':
                $startDate = Carbon::now()->addDays(rand(1, 60));
                $status = ['pending', 'approved'][array_rand(['pending', 'approved'])];
                break;
        }

        $endDate = $startDate->copy()->addDays($duration - 1);

        $reasons = [
            'annual' => 'Family vacation and personal time',
            'sick' => 'Flu and medical treatment',
            'emergency' => 'Family emergency situation',
            'study' => 'Professional certification exam'
        ];

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'tenant_id' => $employee->tenant_id,
            'leave_type' => $leaveType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $duration,
            'day_type' => 'full_day',
            'reason' => $reasons[$leaveType],
            'description' => rand(0, 1) ? 'Additional details for leave request.' : null,
            'is_medical' => $leaveType === 'sick',
            'is_paid' => $leaveType !== 'unpaid',
            'status' => $status,
            'emergency_contact' => '+971' . rand(50, 56) . rand(1000000, 9999999),
        ]);

        if ($status === 'approved') {
            $approver = $this->getRandomManager($employee->tenant_id);
            if ($approver) {
                $leave->update([
                    'approved_by' => $approver->id,
                    'approved_at' => $startDate->copy()->subDays(rand(1, 3)),
                ]);
            }
        } elseif ($status === 'rejected') {
            $rejector = $this->getRandomManager($employee->tenant_id);
            if ($rejector) {
                $leave->update([
                    'rejected_by' => $rejector->id,
                    'rejected_at' => $startDate->copy()->subDays(rand(1, 2)),
                    'rejection_reason' => 'Insufficient leave balance or scheduling conflict',
                ]);
            }
        }

        return $leave;
    }

    private function createCommentsForLeave(Leave $leave): int
    {
        $commentsCreated = 0;
        $commentCount = rand(1, 3);

        // تعليق تلقائي لتقديم الطلب (فقط إذا كان الموظف موجود)
        if ($leave->employee && $leave->employee->user_id) {
            LeaveComment::create([
                'leave_id' => $leave->id,
                'user_id' => $leave->employee->user_id,
                'tenant_id' => $leave->tenant_id,
                'comment' => 'Leave request submitted for review.',
                'comment_type' => 'general',
                'is_system_generated' => true,
                'system_action' => 'submitted',
                'created_at' => $leave->created_at,
            ]);
            $commentsCreated++;
        }

        // تعليقات إضافية
        for ($i = 1; $i < $commentCount; $i++) {
            $users = User::where('tenant_id', $leave->tenant_id)->get();
            $commenter = $users->random();
            
            $comments = [
                'Please let me know if you need any additional information.',
                'I have arranged coverage for my responsibilities.',
                'Will be available on phone for urgent matters.',
                'Thank you for considering my request.',
                'All pending tasks have been delegated.'
            ];

            LeaveComment::create([
                'leave_id' => $leave->id,
                'user_id' => $commenter->id,
                'tenant_id' => $leave->tenant_id,
                'comment' => $comments[array_rand($comments)],
                'comment_type' => 'general',
                'is_system_generated' => false,
                'visibility' => 'public',
                'created_at' => $leave->created_at->copy()->addMinutes(rand(10, 720)),
            ]);
            $commentsCreated++;
        }

        return $commentsCreated;
    }

    private function getRandomManager(int $tenantId): ?User
    {
        return User::where('tenant_id', $tenantId)
                   ->whereHas('roles', function ($query) {
                       $query->whereIn('name', ['Admin', 'Manager']);
                   })
                   ->inRandomOrder()
                   ->first();
    }

    private function getRandomUser(int $tenantId): User
    {
        return User::where('tenant_id', $tenantId)
                   ->inRandomOrder()
                   ->first();
    }
}
