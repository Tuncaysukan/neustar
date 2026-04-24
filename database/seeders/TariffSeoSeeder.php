<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TariffSeoContent;
use Illuminate\Database\Seeder;

class TariffSeoSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ── İL SAYFALARI ──────────────────────────────────────────────
            [
                'page_key'        => TariffSeoContent::cityKey('istanbul'),
                'city_slug'       => 'istanbul',
                'city_name'       => 'İstanbul',
                'district_slug'   => null,
                'district_name'   => null,
                'h1_title'        => 'İstanbul Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'İstanbul\'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir. Fiber, VDSL ve ADSL seçeneklerini karşılaştırın, en uygun paketi bulun.',
                'seo_footer_text' => "İstanbul, Türkiye'nin en büyük şehri olarak fiber internet altyapısının en yaygın olduğu illerden biridir.\n\nAvrupa ve Anadolu yakasındaki ilçelerin büyük çoğunluğunda Türk Telekom, Turkcell Superonline ve diğer operatörlerin fiber altyapısı mevcuttur. Özellikle Kadıköy, Beşiktaş, Şişli, Bakırköy ve Beylikdüzü gibi merkezi ilçelerde 1 Gbps'e kadar fiber hız seçenekleri sunulmaktadır.\n\nİstanbul'da ev interneti seçerken altyapı türüne (Fiber/VDSL/ADSL), taahhüt süresine ve aylık ücrete dikkat etmenizi öneririz.",
                'meta_title'      => 'İstanbul Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'İstanbul\'da en uygun ev interneti paketlerini karşılaştırın. Fiber, VDSL ve ADSL tarifeleri, güncel fiyatlar ve operatör kampanyaları tek sayfada.',
            ],
            [
                'page_key'        => TariffSeoContent::cityKey('ankara'),
                'city_slug'       => 'ankara',
                'city_name'       => 'Ankara',
                'district_slug'   => null,
                'district_name'   => null,
                'h1_title'        => 'Ankara Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'Ankara\'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "Ankara, Türkiye'nin başkenti olarak fiber internet altyapısının güçlü olduğu illerden biridir. Çankaya, Keçiören, Yenimahalle ve Etimesgut gibi merkezi ilçelerde fiber bağlantı seçenekleri yaygın biçimde sunulmaktadır.",
                'meta_title'      => 'Ankara Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'Ankara\'da en uygun ev interneti paketlerini karşılaştırın. Güncel fiyatlar ve operatör kampanyaları.',
            ],
            [
                'page_key'        => TariffSeoContent::cityKey('izmir'),
                'city_slug'       => 'izmir',
                'city_name'       => 'İzmir',
                'district_slug'   => null,
                'district_name'   => null,
                'h1_title'        => 'İzmir Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'İzmir\'deki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "İzmir, Ege'nin en büyük şehri olarak fiber altyapısının hızla geliştiği illerden biridir. Konak, Karşıyaka, Bornova ve Buca ilçelerinde fiber internet seçenekleri geniş ölçüde mevcuttur.",
                'meta_title'      => 'İzmir Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'İzmir\'de en uygun ev interneti paketlerini karşılaştırın. Güncel fiyatlar ve operatör kampanyaları.',
            ],

            // ── İLÇE SAYFALARI ────────────────────────────────────────────
            [
                'page_key'        => TariffSeoContent::districtKey('istanbul', 'pendik'),
                'city_slug'       => 'istanbul',
                'city_name'       => 'İstanbul',
                'district_slug'   => 'pendik',
                'district_name'   => 'Pendik',
                'h1_title'        => 'Pendik Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'İstanbul Pendik\'teki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "Pendik, İstanbul'un Anadolu yakasında yer alan ve hızla gelişen bir ilçesidir.\n\nKurtköy, Kaynarca, Yenişehir ve Batı Pendik mahallelerinde fiber altyapı büyük ölçüde yaygınlaşmıştır. Özellikle Kurtköy ve çevresinde 1 Gbps'e kadar fiber hız seçenekleri mevcuttur.\n\nPendik'te internet aboneliği için Türk Telekom, Turkcell Superonline ve diğer operatörlerin tekliflerini karşılaştırmanızı öneririz.",
                'meta_title'      => 'Pendik Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'İstanbul Pendik\'te en uygun ev interneti paketlerini karşılaştırın. Fiber, VDSL tarifeleri ve güncel kampanyalar.',
            ],
            [
                'page_key'        => TariffSeoContent::districtKey('istanbul', 'beylikduzu'),
                'city_slug'       => 'istanbul',
                'city_name'       => 'İstanbul',
                'district_slug'   => 'beylikduzu',
                'district_name'   => 'Beylikdüzü',
                'h1_title'        => 'Beylikdüzü Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'İstanbul Beylikdüzü\'ndeki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "Beylikdüzü, İstanbul'un Avrupa yakasında hızla büyüyen modern bir ilçesidir.\n\nGüzelyurt, Büyükşehir, Cumhuriyet ve Barış mahallelerinde fiber altyapı yaygın biçimde mevcuttur. Yeni yapılaşma alanlarında FTTH (eve kadar fiber) altyapısı tercih edilmektedir.\n\nBeylikdüzü'nde internet aboneliği için taahhüt süresi ve kurulum ücretine dikkat etmenizi öneririz.",
                'meta_title'      => 'Beylikdüzü Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'İstanbul Beylikdüzü\'nde en uygun ev interneti paketlerini karşılaştırın. Fiber tarifeleri ve güncel kampanyalar.',
            ],
            [
                'page_key'        => TariffSeoContent::districtKey('istanbul', 'kadikoy'),
                'city_slug'       => 'istanbul',
                'city_name'       => 'İstanbul',
                'district_slug'   => 'kadikoy',
                'district_name'   => 'Kadıköy',
                'h1_title'        => 'Kadıköy Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'İstanbul Kadıköy\'deki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "Kadıköy, İstanbul'un Anadolu yakasının en merkezi ilçesi olup fiber internet altyapısı son derece gelişmiştir.\n\nModa, Bağdat Caddesi, Fenerbahçe ve Göztepe mahallelerinde tüm operatörlerin fiber altyapısı mevcuttur. Kadıköy'de 1 Gbps'e kadar simetrik fiber hız seçenekleri bulunmaktadır.",
                'meta_title'      => 'Kadıköy Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'İstanbul Kadıköy\'de en uygun ev interneti paketlerini karşılaştırın. Fiber tarifeleri ve güncel kampanyalar.',
            ],
            [
                'page_key'        => TariffSeoContent::districtKey('ankara', 'cankaya'),
                'city_slug'       => 'ankara',
                'city_name'       => 'Ankara',
                'district_slug'   => 'cankaya',
                'district_name'   => 'Çankaya',
                'h1_title'        => 'Çankaya Ev İnternet Kampanyaları ve Fiyat Karşılaştırma',
                'intro_text'      => 'Ankara Çankaya\'daki ev interneti kullanıcıları için hazırladığımız güncel tarife listesidir.',
                'seo_footer_text' => "Çankaya, Ankara'nın en merkezi ve köklü ilçesi olup fiber internet altyapısı oldukça gelişmiştir. Kızılay, Bahçelievler, Çukurambar ve Oran mahallelerinde tüm operatörlerin fiber altyapısı mevcuttur.",
                'meta_title'      => 'Çankaya Ev İnternet Tarifeleri 2026 — En Ucuz Kampanyalar | Neustar',
                'meta_description'=> 'Ankara Çankaya\'da en uygun ev interneti paketlerini karşılaştırın.',
            ],
        ];

        foreach ($items as $item) {
            TariffSeoContent::updateOrCreate(
                ['page_key' => $item['page_key']],
                $item
            );
        }

        $this->command->info('✓ ' . count($items) . ' tarife SEO içeriği oluşturuldu.');
    }
}
