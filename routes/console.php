<?php

use App\Models\ProfilVisit;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    ProfilVisit::where('created_at', '<', now('APP_TIMEZONE')->subDay())->delete();
})->daily();