<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$enableDailyReport = \App\Models\Setting::get('enable_daily_report', true);
if ($enableDailyReport) {
    $time = \App\Models\Setting::get('daily_report_time', '23:50');
    Schedule::command('app:send-daily-admin-report')->dailyAt($time);
}
