<?php

namespace Database\Seeders;

use App\Models\TariffSeoContent;
use Illuminate\Database\Seeder;

class TariffSeoSeeder extends Seeder
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
                'h1_title' => 'Kayseri Pınarbaşı Ev İnternet Kampanyaları 2026 - Altyapı Sorgulama',
                'intro_text' => 'Pınarbaşı ilçesindeki en iyi internet sağlayıcılarını (Türk Telekom, Turkcell Superonline, Vodafone, Netspeed vb.) karşılaştırın. Fiber, VDSL veya ADSL altyapınıza en uygun paketi seçerek hemen başvurun.',
                'seo_footer_text' => "Kayseri'nin Pınarbaşı ilçesinde internet altyapısı her geçen gün gelişmektedir. Türk Telekom fiber altyapısı ilçe merkezindeki birçok mahallede (Gümüşgün, Hürriyet, Yenice vb.) aktif durumdadır. Ayrıca Türksat Kablonet ve Superonline fiber seçenekleri de bazı sitelerde sunulmaktadır.\n\nİnternet bağlatmadan önce altyapı sorgulaması yapmanız ve port durumunu kontrol etmeniz önerilir. En uygun fiyatlı Pınarbaşı internet paketleri için sitemizdeki karşılaştırma aracını kullanabilir, hız ve fiyat dengesine göre en doğru kararı verebilirsiniz.",
                'faqs' => [
                    [
                        'question' => 'Pınarbaşı\'nda hangi internet sağlayıcıları var?',
                        'answer' => 'Pınarbaşı ilçesinde Türk Telekom, Turkcell Superonline, Vodafone Home, Netspeed ve Millenicom gibi birçok sağlayıcı hizmet vermektedir.'
                    ],
                    [
                        'question' => 'Pınarbaşı\'nda fiber internet altyapısı var mı?',
                        'answer' => 'Evet, Pınarbaşı merkez mahallelerinin çoğunda Türk Telekom fiber (FTTH) altyapısı mevcuttur. Diğer bölgelerde ise VDSL (Hipernet) ve ADSL (Yalın İnternet) seçenekleri sunulmaktadır.'
                    ],
                    [
                        'question' => 'Pınarbaşı için en ucuz internet paketi ne kadar?',
                        'answer' => 'Fiyatlar kampanyalara ve taahhüt sürelerine göre değişmekle birlikte, Pınarbaşı için en uygun giriş seviyesi paketler aylık 199 TL ile 249 TL bandından başlamaktadır.'
                    ],
                    [
                        'question' => 'Altyapı sorgulama sonucu port yok diyor, ne yapmalıyım?',
                        'answer' => 'Eğer sorgulama sonucunda "Boş Port Yok" uyarısı alıyorsanız, uzman ekibimizle iletişime geçerek bölgedeki genişleme çalışmalarını ve alternatif (uzay/kablosuz internet) seçeneklerini öğrenebilirsiniz.'
                    ]
                ],
                'meta_title' => 'Kayseri Pınarbaşı İnternet Kampanyaları 2026 - Altyapı Sorgula',
                'meta_description' => 'Kayseri Pınarbaşı ev interneti fiyatları ve kampanyalarını karşılaştırın. Fiber ve VDSL altyapı seçenekleri ile en uygun interneti hemen bulun. Ücretsiz altyapı sorgulama.'
            ]
        );
    }
}
