<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // فحص انتهاء صلاحية المستندات يومياً في الساعة 9 صباحاً
        $schedule->command('documents:check-expiry --days=30')
                 ->dailyAt('09:00')
                 ->timezone('Asia/Dubai')
                 ->description('Check for documents expiring in 30 days');

        // فحص انتهاء صلاحية المستندات أسبوعياً للمستندات المنتهية خلال 7 أيام
        $schedule->command('documents:check-expiry --days=7')
                 ->weeklyOn(1, '10:00') // كل يوم اثنين الساعة 10 صباحاً
                 ->timezone('Asia/Dubai')
                 ->description('Check for documents expiring in 7 days');

        // فحص يومي للمستندات المنتهية خلال 3 أيام (تنبيه عاجل)
        $schedule->command('documents:check-expiry --days=3')
                 ->dailyAt('08:00')
                 ->timezone('Asia/Dubai')
                 ->description('Urgent check for documents expiring in 3 days');

        // فحص مستندات الموظفين يومياً في الساعة 8:30 صباحاً
        $schedule->command('employees:check-documents --days=30')
                 ->dailyAt('08:30')
                 ->timezone('Asia/Dubai')
                 ->description('Check for employee documents expiring in 30 days');

        // فحص عاجل لمستندات الموظفين يومياً في الساعة 7:30 صباحاً
        $schedule->command('employees:check-documents --days=7')
                 ->dailyAt('07:30')
                 ->timezone('Asia/Dubai')
                 ->description('Urgent check for employee documents expiring in 7 days');

        // فحص عقود الموظفين أسبوعياً يوم الثلاثاء الساعة 9 صباحاً
        $schedule->command('employees:check-documents --days=30 --type=contract')
                 ->weeklyOn(2, '09:00')
                 ->timezone('Asia/Dubai')
                 ->description('Check for employee contracts expiring in 30 days');

        // فحص فيز الموظفين أسبوعياً يوم الأربعاء الساعة 9 صباحاً
        $schedule->command('employees:check-documents --days=30 --type=visa')
                 ->weeklyOn(3, '09:00')
                 ->timezone('Asia/Dubai')
                 ->description('Check for employee visas expiring in 30 days');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
