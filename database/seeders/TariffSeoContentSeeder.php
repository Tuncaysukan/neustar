<?php

namespace Database\Seeders;

use App\Models\TariffSeoContent;
use Illuminate\Database\Seeder;

class TariffSeoContentSeeder extends Seeder
{
    public function run()
    {
        TariffSeoContent::updateOrCreate(
            ['page_key' => 'tariff_district:kayseri:pinarbasi'],
            [
                'city_slug' => 'kayseri',
                'city_name' => 'Kayseri',
                'district_slug' => 'pinarbasi',
                'district_name' => 'Pınarbaşı',
                'meta_title' => 'Pınarbaşı Ev İnterneti Fiyatları & Altyapı Sorgulama 2024',
                'meta_description' => 'Kayseri Pınarbaşı ilçesi için en uygun ev interneti paketlerini karşılaştırın. Fiber, VDSL ve ADSL altyapı sorgulama sonuçlarına göre hemen başvurun.',
                'h1_title' => 'Pınarbaşı Ev İnterneti Paket Karşılaştırma',
                'intro_text' => 'Pınarbaşı ilçesinde yaşayanlar için hazırladığımız bu rehberde, bölgedeki internet sağlayıcılarını ve en güncel tarifeleri bulabilirsiniz.',
                'seo_footer_text' => 'Kayseri Pınarbaşı internet altyapısı TurkNet, Türk Telekom ve diğer operatörler tarafından sağlanmaktadır. Altyapı sorgulama aracımızı kullanarak binanızdaki hızı öğrenebilirsiniz.',
                'faqs' => [
                    [
                        'question' => 'Pınarbaşı\'nda hangi internet altyapıları var?',
                        'answer' => 'Pınarbaşı genelinde Türk Telekom ADSL/VDSL altyapısı yaygındır. Belirli bölgelerde Fiber altyapı çalışmaları devam etmektedir.'
                    ],
                    [
                        'question' => 'En ucuz internet paketi hangisi?',
                        'answer' => 'Şu an Pınarbaşı için en ekonomik paketler 16 Mbps ADSL tarifeleridir. Güncel fiyatları yukarıdaki listeden karşılaştırabilirsiniz.'
                    ]
                ],
            ]
        );
    }
}
