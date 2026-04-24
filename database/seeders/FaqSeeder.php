<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Operator;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Önce operatörleri al
        $turkTelekom = Operator::where('slug', 'turk-telekom')->first();
        $superonline = Operator::where('slug', 'turkcell-superonline')->first();
        $vodafone = Operator::where('slug', 'vodafone-net')->first();
        $turknet = Operator::where('slug', 'turknet')->first();

        $faqs = [
            // Genel SSS'ler
            [
                'question' => 'İnternet paketi nasıl seçmeliyim?',
                'answer' => 'İnternet paketi seçerken öncelikle kullanım alışkanlıklarınızı değerlendirin. Evden çalışıyorsanız, online oyun oynuyorsanız veya 4K video izliyorsanız yüksek hızlı fiber internet paketlerini tercih edin. Sadece sosyal medya ve e-posta kullanıyorsanız daha düşük hızlı paketler yeterli olacaktır.',
                'page_type' => 'general',
                'relation_id' => null,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'question' => 'Fiber internet ile VDSL arasındaki fark nedir?',
                'answer' => 'Fiber internet, ışık hızında veri iletimi sağlayan en modern altyapıdır ve çok daha yüksek hızlar sunar. VDSL ise bakır telefon hatları üzerinden çalışır ve fiber kadar hızlı değildir. Fiber altyapısı varsa her zaman fiber tercih edilmelidir.',
                'page_type' => 'general',
                'relation_id' => null,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'question' => 'Taahhütsüz internet mümkün mü?',
                'answer' => 'Evet, bazı operatörler taahhütsüz internet paketleri sunmaktadır. Özellikle Türknet altyapı fark etmeksizin tek fiyatla taahhütsüz internet hizmeti vermektedir. Diğer operatörlerde de taahhütsüz seçenekler bulunabilir ancak fiyatlar taahhütlü paketlere göre daha yüksek olabilir.',
                'page_type' => 'general',
                'relation_id' => null,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'question' => 'Modemi kendim almalı mıyım?',
                'answer' => 'Operatörler genellikle kampanya dahilinde ücretsiz modem vermektedir. Ancak kendi modeminizi almak isterseniz, altyapınıza uyumlu ve yüksek performanslı bir modem tercih edebilirsiniz. Fiber internet için GPON uyumlu modem, VDSL/ADSL için uyumlu bir modem seçmelisiniz.',
                'page_type' => 'general',
                'relation_id' => null,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'question' => 'Kurulum ücreti ne kadar?',
                'answer' => 'Kurulum ücretleri operatörlere ve kampanyalara göre değişmektedir. Birçok operatör yeni abonelere ücretsiz kurulum sunarken, bazı durumlarda 100-200 TL arası kurulum ücreti alınabilir. Kampanya dönemlerinde ücretsiz kurulum fırsatlarını değerlendirebilirsiniz.',
                'page_type' => 'general',
                'relation_id' => null,
                'is_active' => true,
                'order' => 5,
            ],
        ];

        // Operatöre özel SSS'ler
        if ($turkTelekom) {
            $faqs[] = [
                'question' => 'Türk Telekom fiber internet hızları nelerdir?',
                'answer' => 'Türk Telekom fiber internet paketleri 100 Mbps, 200 Mbps, 500 Mbps ve 1000 Mbps hız seçenekleri sunmaktadır. Hız seçenekleri bulunduğunuz adresteki altyapıya göre değişiklik gösterebilir.',
                'page_type' => 'operator',
                'relation_id' => $turkTelekom->id,
                'is_active' => true,
                'order' => 1,
            ];
            $faqs[] = [
                'question' => 'Türk Telekom\'da taahhüt süresi ne kadar?',
                'answer' => 'Türk Telekom internet paketlerinde genellikle 12 ay veya 24 ay taahhüt seçenekleri bulunmaktadır. Taahhütlü paketler daha avantajlı fiyatlar sunarken, taahhütsüz seçenekler de mevcuttur.',
                'page_type' => 'operator',
                'relation_id' => $turkTelekom->id,
                'is_active' => true,
                'order' => 2,
            ];
            $faqs[] = [
                'question' => 'Türk Telekom müşteri hizmetlerine nasıl ulaşabilirim?',
                'answer' => 'Türk Telekom müşteri hizmetlerine 444 0 375 numaralı telefondan 7/24 ulaşabilirsiniz. Ayrıca Türk Telekom Online İşlemler uygulaması ve web sitesi üzerinden de işlemlerinizi gerçekleştirebilirsiniz.',
                'page_type' => 'operator',
                'relation_id' => $turkTelekom->id,
                'is_active' => true,
                'order' => 3,
            ];
        }

        if ($superonline) {
            $faqs[] = [
                'question' => 'Superonline GigaFiber nedir?',
                'answer' => 'Superonline GigaFiber, 1000 Mbps\'ye varan eşit upload ve download hızları sunan fiber internet teknolojisidir. Özellikle yoğun internet kullanıcıları, evden çalışanlar ve oyun tutkunları için idealdir.',
                'page_type' => 'operator',
                'relation_id' => $superonline->id,
                'is_active' => true,
                'order' => 1,
            ];
            $faqs[] = [
                'question' => 'Superonline\'da kampanyalar nasıl takip edilir?',
                'answer' => 'Superonline kampanyalarını resmi web sitesi, mobil uygulama ve çağrı merkezi üzerinden takip edebilirsiniz. Yeni müşterilere özel ilk 3 ay %50 indirim gibi avantajlar düzenli olarak sunulmaktadır.',
                'page_type' => 'operator',
                'relation_id' => $superonline->id,
                'is_active' => true,
                'order' => 2,
            ];
        }

        if ($vodafone) {
            $faqs[] = [
                'question' => 'Vodafone Red müşterilerine özel indirim var mı?',
                'answer' => 'Evet, Vodafone Red ve Black tarife sahipleri sabit internet paketlerinde özel indirimlerden yararlanabilir. Ayrıca ev interneti ile mobil hat birleştirme avantajlarıyla ekstra fatura indirimleri sağlanmaktadır.',
                'page_type' => 'operator',
                'relation_id' => $vodafone->id,
                'is_active' => true,
                'order' => 1,
            ];
            $faqs[] = [
                'question' => 'Vodafone Net\'te kurulum ne kadar sürer?',
                'answer' => 'Vodafone Net fiber internet kurulumu, altyapı uygunluğuna göre 3-7 iş günü içinde tamamlanmaktadır. Başvuru sonrası teknik ekip adresinizde kontrol yaparak kurulum tarihi belirlenir.',
                'page_type' => 'operator',
                'relation_id' => $vodafone->id,
                'is_active' => true,
                'order' => 2,
            ];
        }

        if ($turknet) {
            $faqs[] = [
                'question' => 'Türknet\'te altyapı fark etmeksizin tek fiyat ne demek?',
                'answer' => 'Türknet, fiber altyapıda ve VDSL/ADSL altyapıda aynı fiyatı uygulayan tek operatördür. Bu sayede altyapınız ne olursa olsun adil ve şeffaf bir fiyatlandırma ile karşılaşırsınız.',
                'page_type' => 'operator',
                'relation_id' => $turknet->id,
                'is_active' => true,
                'order' => 1,
            ];
            $faqs[] = [
                'question' => 'Türknet taahhütsüz internet avantajları nelerdir?',
                'answer' => 'Türknet taahhütsüz internet paketleri ile 12 veya 24 ay bağlı kalmadan istediğiniz zaman iptal edebilirsiniz. Bu esneklik, taahhüt korkusu olmadan internet hizmeti almanızı sağlar.',
                'page_type' => 'operator',
                'relation_id' => $turknet->id,
                'is_active' => true,
                'order' => 2,
            ];
            $faqs[] = [
                'question' => 'Türknet müşteri hizmetleri nasıl?',
                'answer' => 'Türknet, ödüllü müşteri hizmetleri ile bilinir. Hızlı teknik destek, Türkçe çağrı merkezi ve online destek kanalları ile kullanıcılarına hizmet vermektedir. Ayrıca sosyal medya üzerinden de destek alabilirsiniz.',
                'page_type' => 'operator',
                'relation_id' => $turknet->id,
                'is_active' => true,
                'order' => 3,
            ];
        }

        // Karşılaştırma sayfası SSS'leri
        $faqs[] = [
            'question' => 'Paket karşılaştırma nasıl çalışır?',
            'answer' => 'Paket karşılaştırma aracımızda en fazla 5 paketi yan yana getirebilirsiniz. Hız, fiyat, kota, taahhüt süresi ve altyapı türü gibi özellikleri karşılaştırarak size en uygun paketi kolayca seçebilirsiniz.',
            'page_type' => 'compare',
            'relation_id' => null,
            'is_active' => true,
            'order' => 1,
        ];
        $faqs[] = [
            'question' => 'Karşılaştırma sonucunda nasıl başvuru yaparım?',
            'answer' => 'Karşılaştırma sonucunda beğendiğiniz paketin üzerine tıklayarak detay sayfasına gidebilirsiniz. Detay sayfasında "Başvur" butonuna tıklayarak operatörün resmi web sitesine yönlendirilir ve başvurunuzu tamamlayabilirsiniz.',
            'page_type' => 'compare',
            'relation_id' => null,
            'is_active' => true,
            'order' => 2,
        ];
        $faqs[] = [
            'question' => 'Fiyatlar güncel mi?',
            'answer' => 'Evet, sitemizdeki fiyatlar düzenli olarak güncellenmektedir. Ancak operatörler kampanya dönemlerinde fiyat değişikliği yapabilir. Kesin fiyat bilgisi için operatörün resmi web sitesini kontrol etmenizi öneririz.',
            'page_type' => 'compare',
            'relation_id' => null,
            'is_active' => true,
            'order' => 3,
        ];

        // Hız testi sayfası SSS'leri
        $faqs[] = [
            'question' => 'Hız testi nasıl çalışır?',
            'answer' => 'Hız testimiz Cloudflare\'in küresel edge ağı üzerinden gerçek trafikle ölçüm yapar. İndirme hızı, yükleme hızı, ping, jitter ve paket kaybı gibi metrikleri saniyeler içinde ölçer.',
            'page_type' => 'speed_test',
            'relation_id' => null,
            'is_active' => true,
            'order' => 1,
        ];
        $faqs[] = [
            'question' => 'Hız testi sonuçları neden farklı çıkıyor?',
            'answer' => 'Hız testi sonuçları; kullandığınız cihaz, bağlantı türü (WiFi/Ethernet), ağ trafiği, test sunucusunun konumu ve aynı anda internet kullanan diğer cihazlar gibi faktörlere göre değişebilir. En doğru sonuç için Ethernet bağlantısı kullanarak ve diğer cihazları kapatarak test yapın.',
            'page_type' => 'speed_test',
            'relation_id' => null,
            'is_active' => true,
            'order' => 2,
        ];
        $faqs[] = [
            'question' => 'Ping ve jitter ne anlama geliyor?',
            'answer' => 'Ping, cihazınızdan sunucuya gönderilen veri paketinin gitmesi ve geri dönmesi için geçen süredir (ms). Düşük ping, online oyun ve görüntülü görüşme için önemlidir. Jitter ise ping değerlerinin dalgalanma miktarıdır. Düşük jitter, daha stabil bir bağlantı demektir.',
            'page_type' => 'speed_test',
            'relation_id' => null,
            'is_active' => true,
            'order' => 3,
        ];

        // Taahhüt sayacı sayfası SSS'leri
        $faqs[] = [
            'question' => 'Taahhüt sayacı ne işe yarar?',
            'answer' => 'Taahhüt sayacı, internet paketinizin taahhüt bitiş tarihini hesaplamanıza ve kalan gün/ayı görmenizi sağlar. Böylece taahhüdünüz bitmeden yeni kampanyaları takip edebilir ve geçiş planlaması yapabilirsiniz.',
            'page_type' => 'commitment',
            'relation_id' => null,
            'is_active' => true,
            'order' => 1,
        ];
        $faqs[] = [
            'question' => 'Hatırlatıcı nasıl çalışır?',
            'answer' => 'Taahhüt bitimine yakın e-posta veya SMS ile bildirim almak için hatırlatıcı kurabilirsiniz. Taahhüdünüzün bitmesine 30, 15 ve 7 gün kala size otomatik hatırlatma gönderilir.',
            'page_type' => 'commitment',
            'relation_id' => null,
            'is_active' => true,
            'order' => 2,
        ];
        $faqs[] = [
            'question' => 'Cayma bedeli nedir ve nasıl hesaplanır?',
            'answer' => 'Cayma bedeli, taahhüt süresi dolmadan internet sözleşmenizi feshettiğinizde operatörün talep ettiği bedeldir. Genellikle "kalan ay sayısı × aylık ücret" formülüyle hesaplanır. Bazı operatörler yeni abonelere cayma bedeli desteği sunabilir.',
            'page_type' => 'commitment',
            'relation_id' => null,
            'is_active' => true,
            'order' => 3,
        ];
        $faqs[] = [
            'question' => 'Taahhüt bitmeden operatör değiştirebilir miyim?',
            'answer' => 'Evet, taahhüt bitmeden operatör değiştirebilirsiniz ancak bu durumda cayma bedeli ödemeniz gerekir. Bazı operatörler yeni abonelere cayma bedeli desteği sunarak bu maliyeti karşılayabilir. Taahhüt bitimine yakın değişiklik yapmak daha avantajlıdır.',
            'page_type' => 'commitment',
            'relation_id' => null,
            'is_active' => true,
            'order' => 4,
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                [
                    'question' => $faq['question'],
                    'page_type' => $faq['page_type'],
                ],
                $faq
            );
        }

        $this->command->info('FAQ seeding completed!');
    }
}
