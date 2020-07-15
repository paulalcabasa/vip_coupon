<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      Commands\Notification::class,
      Commands\GenerateVoucher::class,
      Commands\PromoNotification::class,
      Commands\PromoRequestorNotif::class,
      Commands\ClaimApprovalNotif::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule -> exec("php artisan notification:send");
        $schedule -> exec("php artisan voucher:generate");
        $schedule -> exec("php artisan requestor:send");
        $schedule -> exec("php artisan promo:send");
        $schedule -> exec("php artisan claim_approval:send");
        /* $schedule->command('notification:send')
                  ->everyFiveMinutes();

        $schedule->command('voucher:generate')
                  ->everyFiveMinutes();
        
        $schedule->command('requestor:send')
                  ->everyFiveMinutes();

        $schedule->command('promo:send')
                  ->everyFiveMinutes(); */
                  
                  
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
