<?php

namespace App\Console;

use App\Console\Commands;
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
        Commands\EskilstunaBarnCron::class,
        Commands\EskilstunaBlommorCron::class,
        Commands\EskilstunaBrodCron::class,
        Commands\EskilstunaButikensCron::class,
        Commands\EskilstunaDjurCron::class,
        Commands\EskilstunaDryckCron::class,
        Commands\EskilstunaFardigmatCron::class,
        Commands\EskilstunaFritidCron::class,
        Commands\EskilstunaFruktCron::class,
        Commands\EskilstunaFrystCron::class,
        Commands\EskilstunaGlassCron::class,
        Commands\EskilstunaHalsaCron::class,
        Commands\EskilstunaHemCron::class,
        Commands\EskilstunaInspirationCron::class,
        Commands\EskilstunaKioskCron::class,
        Commands\EskilstunaKokCron::class,
        Commands\EskilstunaKottCron::class,
        Commands\EskilstunaMejeriCron::class,
        Commands\EskilstunaReceptfriaCron::class,
        Commands\EskilstunaSkafferiCron::class,
        Commands\EskilstunaStadCron::class,
        Commands\EskilstunaVegetarisktCron::class,
        Commands\TestCron::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('daily:update-sitemap')
            ->daily();

        $schedule->command('eskilstuna_barn:cron')
            ->hourlyAt(1);

        $schedule->command('eskilstuna_blommor:cron')
            ->hourlyAt(4);

        $schedule->command('eskilstuna_vegetariskt:cron')
            ->hourlyAt(6);

        $schedule->command('eskilstuna_brod:cron')
            ->hourlyAt(8);

        $schedule->command('eskilstuna_butikens:cron')
            ->hourlyAt(12);

        $schedule->command('eskilstuna_skafferi:cron')
            ->hourlyAt(14);

        $schedule->command('eskilstuna_djur:cron')
            ->hourlyAt(16);

        $schedule->command('eskilstuna_dryck:cron')
            ->hourlyAt(20);

        $schedule->command('eskilstuna_fardigmat:cron')
            ->hourlyAt(24);

        $schedule->command('eskilstuna_stad:cron')
            ->hourlyAt(26);

        $schedule->command('eskilstuna_fritid:cron')
            ->hourlyAt(28);

        $schedule->command('eskilstuna_frukt:cron')
            ->hourlyAt(32);

        $schedule->command('eskilstuna_fryst:cron')
            ->hourlyAt(36);

        $schedule->command('eskilstuna_glass:cron')
            ->hourlyAt(40);

        $schedule->command('eskilstuna_halsa:cron')
            ->hourlyAt(43);

        $schedule->command('eskilstuna_hem:cron')
            ->hourlyAt(46);

        $schedule->command('eskilstuna_inspiration:cron')
            ->hourlyAt(49);

        $schedule->command('eskilstuna_kiosk:cron')
            ->hourlyAt(52);

        $schedule->command('eskilstuna_kok:cron')
            ->hourlyAt(54);

        $schedule->command('eskilstuna_kott:cron')
            ->hourlyAt(56);

        $schedule->command('eskilstuna_mejeri:cron')
            ->hourlyAt(58);

        $schedule->command('eskilstuna_receptfria:cron')
            ->hourlyAt(59);

        $schedule->command('test_cron:cron')
            ->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
