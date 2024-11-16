<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::create(['key' => 'ONESIGNAL_APP_ID', 'value' => 'your_onesignal_app_id']);
        Setting::create(['key' => 'ONESIGNAL_REST_API_KEY', 'value' => 'MGIwN2VjYjYtNTNjMy00YjZjLWFlOTgtNGE5MWU4YzBiNjIw']);
        Setting::create(['key' => 'ONESIGNAL_USER_AUTH_KEY', 'value' => 'your_user_auth_key']);
        Setting::create(['key' => 'INFURA_PROJECT_ID', 'value' => '226b7d90c9ea429aa64b907b426c2519']);
        Setting::create(['key' => 'INFURA_API_KEY', 'value' => '2baf6baa7d5e423f852b26e21e7c9872']);
        Setting::create(['key' => 'MAIL_MAILER', 'value' => 'smtp']);
        Setting::create(['key' => 'MAIL_HOST', 'value' => 'mail.chainfinance.com.ng']);
        Setting::create(['key' => 'MAIL_PORT', 'value' => '465']);
        Setting::create(['key' => 'MAIL_USERNAME', 'value' => 'notify@chainfinance.com.ng']);
        Setting::create(['key' => 'MAIL_PASSWORD', 'value' => '66GIJhfkXcuT']);
        Setting::create(['key' => 'MAIL_ENCRYPTION', 'value' => 'ssl']);
        Setting::create(['key' => 'MAIL_FROM_ADDRESS', 'value' => 'notify@chainfinance.com.ng']);
        Setting::create(['key' => 'MAIL_FROM_NAME', 'value' => env('APP_NAME')]);
    }
}
