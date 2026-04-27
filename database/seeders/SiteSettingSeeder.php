<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SiteSetting::defaults() as $default) {
            SiteSetting::firstOrCreate(['key' => $default['key']], $default);
        }
        $this->command->info('Site ayarları oluşturuldu.');
    }
}
