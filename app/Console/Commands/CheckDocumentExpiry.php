<?php

/**
 * Author: Eng.Fahed
 * Check Document Expiry Command for HR System
 * أمر فحص انتهاء صلاحية المستندات القانونية
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckDocumentExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-expiry {--days=30 : Number of days before expiry to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for documents that are expiring soon and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        
        $this->info("🔍 Checking for documents expiring in the next {$days} days...");

        // جلب المستندات المنتهية الصلاحية قريباً
        $expiringDocuments = LegalDocument::with(['tenant', 'branch', 'companySetting'])
            ->expiringSoon($days)
            ->where(function($query) {
                $query->whereNull('last_notification_sent')
                      ->orWhere('last_notification_sent', '<', now()->subDays(7)); // لا نرسل أكثر من مرة في الأسبوع
            })
            ->get();

        if ($expiringDocuments->isEmpty()) {
            $this->info("✅ No documents expiring in the next {$days} days.");
            return Command::SUCCESS;
        }

        $this->info("📄 Found {$expiringDocuments->count()} documents expiring soon:");

        $notificationsSent = 0;
        $errors = 0;

        foreach ($expiringDocuments as $document) {
            try {
                $this->line("  📋 {$document->document_name} ({$document->document_number}) - Expires: {$document->expiry_date->format('Y-m-d')}");
                
                // إرسال تنبيه للمدراء والـ tenant admin
                $this->sendExpiryNotification($document);
                
                // تحديث تاريخ آخر إرسال تنبيه
                $document->update(['last_notification_sent' => now()]);
                
                $notificationsSent++;
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error sending notification for {$document->document_name}: {$e->getMessage()}");
                Log::error("Document expiry notification error", [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
                $errors++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Notifications sent: {$notificationsSent}");
        if ($errors > 0) {
            $this->error("❌ Errors: {$errors}");
        }

        return Command::SUCCESS;
    }

    /**
     * إرسال تنبيه انتهاء الصلاحية
     */
    private function sendExpiryNotification(LegalDocument $document): void
    {
        // جلب المستخدمين المعنيين (tenant admin + managers)
        $users = User::where('tenant_id', $document->tenant_id)
            ->whereIn('user_type', ['tenant_admin'])
            ->orWhereHas('roles', function($query) {
                $query->whereIn('name', ['Admin', 'Manager']);
            })
            ->get();

        $daysUntilExpiry = $document->days_until_expiry;
        
        foreach ($users as $user) {
            try {
                // إرسال بريد إلكتروني (يمكن استبداله بنظام إشعارات داخلي)
                $this->sendEmailNotification($user, $document, $daysUntilExpiry);
                
                // إضافة إشعار داخلي
                $this->createInAppNotification($user, $document, $daysUntilExpiry);
                
            } catch (\Exception $e) {
                Log::error("Failed to send notification to user {$user->id}", [
                    'user_id' => $user->id,
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * إرسال بريد إلكتروني
     */
    private function sendEmailNotification(User $user, LegalDocument $document, int $daysUntilExpiry): void
    {
        // في الوقت الحالي سنسجل فقط - يمكن إضافة Mail::send لاحقاً
        Log::info("Document expiry notification", [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'document_name' => $document->document_name,
            'document_number' => $document->document_number,
            'expiry_date' => $document->expiry_date->format('Y-m-d'),
            'days_until_expiry' => $daysUntilExpiry,
            'renewal_cost' => $document->renewal_cost,
            'issuing_authority' => $document->issuing_authority,
        ]);

        // هنا يمكن إضافة إرسال بريد إلكتروني فعلي
        // Mail::to($user->email)->send(new DocumentExpiryNotification($document, $daysUntilExpiry));
    }

    /**
     * إنشاء إشعار داخلي
     */
    private function createInAppNotification(User $user, LegalDocument $document, int $daysUntilExpiry): void
    {
        // إنشاء إشعار في قاعدة البيانات (يمكن إضافة جدول notifications لاحقاً)
        Log::info("In-app notification created", [
            'user_id' => $user->id,
            'type' => 'document_expiry',
            'title' => "Document Expiry Alert: {$document->document_name}",
            'message' => "The {$document->document_name} ({$document->document_number}) will expire in {$daysUntilExpiry} days on {$document->expiry_date->format('Y-m-d')}. Please renew it with {$document->issuing_authority}.",
            'data' => [
                'document_id' => $document->id,
                'expiry_date' => $document->expiry_date->format('Y-m-d'),
                'renewal_cost' => $document->renewal_cost,
                'currency' => $document->currency,
            ]
        ]);
    }
}