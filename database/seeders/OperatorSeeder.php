<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            [
                'name' => 'Türk Telekom',
                'slug' => 'turk-telekom',
                'website_url' => 'https://www.turktelekom.com.tr',
                'description' => 'Türkiye\'nin en köklü iletişim operatörlerinden biri olan Türk Telekom, fiber ve VDSL altyapısıyla yüksek hızlı internet hizmeti sunmaktadır.',
                'seo_title' => 'Türk Telekom İnternet Paketleri - Neustar',
                'seo_description' => 'Türk Telekom internet paketlerini karşılaştırın, en uygun fiyatlı tarifeleri bulun ve hemen başvurun.',
                'seo_text' => "Türk Telekom İnternet Hizmetleri\n\nTürk Telekom, Türkiye'nin önde gelen telekomünikasyon şirketlerinden biridir. 175 yılı aşkın süredir hizmet veren şirket, modern altyapısı ve geniş kapsama alanıyla milyonlarca kullanıcıya ulaşmaktadır.\n\nFiber İnternet Avantajları\n\nTürk Telekom fiber internet hizmeti, yüksek hız ve stabil bağlantı sunar. 100 Mbps'den başlayıp 1000 Mbps'ye kadar hız seçenekleri bulunmaktadır. Fiber internet, özellikle evden çalışanlar, online oyun oyuncuları ve yüksek çözünürlüklü video izleyenler için idealdir.\n\nVDSL ve ADSL Seçenekleri\n\nFiber altyapının bulunmadığı bölgelerde Türk Telekom VDSL ve ADSL hizmetleri sunmaktadır. Bu hizmetler, mevcut telefon hatları üzerinden internet erişimi sağlar.\n\nKampanya ve İndirimler\n\nTürk Telekom düzenli olarak yeni müşterilere özel kampanyalar düzenlemektedir. İlk 3 ay indirimli tarifeler, modem hediyeleri ve ücretsiz kurulum fırsatlarını değerlendirebilirsiniz.",
                'is_active' => true,
            ],
            [
                'name' => 'Turkcell Superonline',
                'slug' => 'turkcell-superonline',
                'website_url' => 'https://www.superonline.net',
                'description' => 'Turkcell Superonline, fiber internet altyapısıyla evlere ve işletmelere yüksek hızlı internet çözümleri sunar.',
                'seo_title' => 'Turkcell Superonline İnternet Paketleri - Neustar',
                'seo_description' => 'Turkcell Superonline fiber internet paketlerini inceleyin, hız testi yapın ve size en uygun tarifeyi seçin.',
                'seo_text' => "Turkcell Superonline Fiber İnternet\n\nTurkcell Superonline, Türkiye'nin en hızlı fiber internet altyapısına sahip operatörlerinden biridir. GigaFiber teknolojisi ile 1000 Mbps'ye varan hızlar sunmaktadır.\n\nSuperonline Avantajları\n\n- Hızlı kurulum ve aktivasyon\n- 7/24 teknik destek\n- Ücretsiz modem ve kurulum kampanyaları\n- Turkcell mobil hat sahiplerine özel indirimler\n\nPaket Seçenekleri\n\nSuperonline, farklı kullanım ihtiyaçlarına göre çeşitli paketler sunar. Temel paketler 50 Mbps ile başlarken, yüksek hızlı paketler 1000 Mbps'ye kadar çıkmaktadır.\n\nGigaFiber Nedir?\n\nGigaFiber, Superonline'ın en yüksek hızlı internet hizmetidir. 1000 Mbps download ve 1000 Mbps upload hızı ile eşit internet deneyimi yaşarsınız.",
                'is_active' => true,
            ],
            [
                'name' => 'Vodafone Net',
                'slug' => 'vodafone-net',
                'website_url' => 'https://www.vodafone.com.tr',
                'description' => 'Vodafone Net, fiber ve ADSL altyapısıyla güvenilir internet hizmeti sunar. Vodafone mobil hat sahiplerine özel avantajlar sunar.',
                'seo_title' => 'Vodafone Net İnternet Paketleri - Neustar',
                'seo_description' => 'Vodafone Net internet tarifelerini karşılaştırın, Red ve Black müşterilerine özel avantajları keşfedin.',
                'seo_text' => "Vodafone Net İnternet Hizmetleri\n\nVodafone Net, Vodafone Türkiye'nin sabit internet markasıdır. Fiber ve ADSL altyapıları ile Türkiye'nin dört bir yanına internet hizmeti sunmaktadır.\n\nVodafone Red ve Black Avantajları\n\nVodafone mobil hat sahipleri, sabit internet paketlerinde özel indirimlerden yararlanabilir. Red ve Black tarife sahipleri için ekstra indirimler ve ek paket avantajları bulunmaktadır.\n\nFiber İnternet Hızları\n\nVodafone Net fiber internet paketleri 35 Mbps'den başlayıp 1000 Mbps'ye kadar çıkmaktadır. Her bütçeye ve kullanım ihtiyacına uygun paket seçenekleri mevcuttur.\n\nKampanyalar\n\nVodafone Net düzenli olarak yeni abonelere özel kampanyalar düzenlemektedir. Taahhütsüz internet seçenekleri ve ilk 6 ay %50 indirim fırsatlarını kaçırmayın.",
                'is_active' => true,
            ],
            [
                'name' => 'Türknet',
                'slug' => 'turknet',
                'website_url' => 'https://www.turk.net',
                'description' => 'Türknet, altyapı fark etmeksizin tek fiyatla internet sunan ve müşteri memnuniyetini ön planda tutan bir operatördür.',
                'seo_title' => 'Türknet İnternet Paketleri - Neustar',
                'seo_description' => 'Türknet altyapı fark etmeksizin tek fiyat avantajıyla internet hizmeti sunar. Taahhütsüz internet seçeneklerini inceleyin.',
                'seo_text' => "Türknet - Altyapı Fark Etmeksizin Tek Fiyat\n\nTürknet, Türkiye'nin en hızlı büyeyen internet servis sağlayıcılarından biridir. 'Altyapı fark etmeksizin tek fiyat' politikası ile tüketicilere şeffaf fiyatlandırma sunar.\n\nTaahhütsüz İnternet\n\nTürknet'in en büyük avantajlarından biri taahhütsüz internet hizmetidir. 12 veya 24 ay taahhüt vermeden, istediğiniz zaman iptal edebileceğiniz esnek paketler sunar.\n\nFiyatlandırma Politikası\n\nTürknet'te fiber altyapıda ve VDSL/ADSL altyapıda aynı fiyatlar geçerlidir. Bu sayede altyapınız ne olursa olsun adil bir fiyatlandırma ile karşılaşırsınız.\n\nMüşteri Hizmetleri\n\nTürknet, ödüllü müşteri hizmetleri ile bilinir. Hızlı teknik destek, Türkçe çağrı merkezi ve online destek kanalları ile kullanıcılarına hizmet vermektedir.",
                'is_active' => true,
            ],
        ];

        foreach ($operators as $operator) {
            Operator::updateOrCreate(
                ['slug' => $operator['slug']],
                $operator
            );
        }

        $this->command->info('Operator seeding completed!');
    }
}
