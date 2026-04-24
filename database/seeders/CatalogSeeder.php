<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Faq;
use App\Models\InternetPackage;
use App\Models\Operator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $operators = collect([
            ['name' => 'Türk Telekom', 'slug' => 'turk-telekom'],
            ['name' => 'Turkcell Superonline', 'slug' => 'superonline'],
            ['name' => 'Netspeed', 'slug' => 'netspeed'],
            ['name' => 'Millenicom', 'slug' => 'millenicom'],
            ['name' => 'Digiturk', 'slug' => 'digiturk'],
            ['name' => 'D-Smart', 'slug' => 'd-smart'],
        ])->map(fn ($o) => Operator::firstOrCreate(
            ['slug' => $o['slug']],
            [
                'name' => $o['name'],
                'description' => $o['name'].' hakkında kısa bilgi.',
                'is_active' => true,
            ],
        ));

        $bySlug = $operators->keyBy('slug');

        $packages = [
            [
                'operator' => 'digiturk',
                'name' => 'Digiturk 100 Mbps İnternet İlk 3 ay İndirimli Kampanya',
                'price' => 349.00,
                'speed' => 100,
                'upload_speed' => 20,
                'quota' => 'Sınırsız',
                'commitment_period' => 12,
                'infrastructure_type' => 'Fiber',
                'advantages' => "İlk 3 ay sabit 349 TL avantajı\n100 Mbps yüksek hızda internet\nDigiturk spor ve eğlence içerikleri",
                'disadvantages' => "12 ay taahhüt şartı\nCayma durumunda indirim iadesi olabilir",
                'is_sponsored' => true,
            ],
            [
                'operator' => 'netspeed',
                'name' => 'Netspeed 100 Mbps İnternet İlk 3 Ay İndirimli Kampanya',
                'price' => 399.90,
                'speed' => 100,
                'upload_speed' => 20,
                'quota' => 'Sınırsız',
                'commitment_period' => 0,
                'infrastructure_type' => 'ADSL',
                'advantages' => "100 Mbps’e kadar indirme hızı\nLimitsiz internet kullanımı\nTaahhüt zorunluluğu yok",
                'disadvantages' => "Bölgeye göre altyapı değişebilir",
                'is_sponsored' => true,
            ],
            [
                'operator' => 'digiturk',
                'name' => 'Digiturk’e Geçiş Özel: 35 Mbps Sınırsız İnternet + Taraftar Paketi Kampanyası',
                'price' => 499.90,
                'speed' => 35,
                'upload_speed' => 8,
                'quota' => 'Sınırsız',
                'commitment_period' => 12,
                'infrastructure_type' => 'Fiber',
                'advantages' => "35 Mbps’ye kadar yüksek hızlı ve sınırsız internet\n12 ay ücretsiz Taraftar Paketi\n2.500 TL cayma bedeli karşılama desteği",
                'disadvantages' => "12 ay taahhüt şartı",
                'is_sponsored' => true,
            ],
        ];

        foreach ($packages as $p) {
            $slug = Str::slug($p['name']);
            InternetPackage::firstOrCreate(
                ['slug' => $slug],
                [
                    'operator_id' => $bySlug[$p['operator']]->id,
                    'name' => $p['name'],
                    'price' => $p['price'],
                    'speed' => $p['speed'],
                    'upload_speed' => $p['upload_speed'],
                    'quota' => $p['quota'],
                    'commitment_period' => $p['commitment_period'],
                    'infrastructure_type' => $p['infrastructure_type'],
                    'description' => 'Kampanya detayları operatöre göre değişebilir.',
                    'advantages' => $p['advantages'],
                    'disadvantages' => $p['disadvantages'],
                    'is_active' => true,
                    'is_sponsored' => (bool) $p['is_sponsored'],
                    'seo_title' => $p['name'].' - Neustar',
                    'seo_description' => Str::limit(strip_tags($p['advantages']), 155),
                ],
            );
        }

        // --- Blog Categories ---
        $genelCat = \App\Models\BlogCategory::firstOrCreate(
            ['slug' => 'genel'],
            ['name' => 'Genel', 'is_active' => true]
        );

        $blogs = [
            [
                'title' => 'Digiturk’ten Sağlık Çalışanlarına Özel Kampanya 2026 | En Uygun İnternet + TV Paketleri',
                'image' => 'https://images.unsplash.com/photo-1526256262350-7da7584cf5eb?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'title' => 'ChatGPT’ye Göre En İyi İnternet Siteleri (2026) – En Uygun Ev İnterneti Nasıl Seçilir?',
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1400&q=80',
            ],
            [
                'title' => 'Gemini’ye Göre En İyi İnternet Siteleri (2026) – En Uygun Ev İnterneti ve Karşılaştırma Rehberi',
                'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1400&q=80',
            ],
        ];

        foreach ($blogs as $b) {
            $slug = Str::slug($b['title']);
            Blog::firstOrCreate(
                ['slug' => $slug],
                [
                    'blog_category_id' => $genelCat->id,
                    'title' => $b['title'],
                    'content' => 'Bu içerik örnek (seed) amaçlı eklenmiştir. Admin panelden güncelleyebilirsiniz.',
                    'image' => $b['image'],
                    'is_active' => true,
                    'seo_title' => $b['title'],
                    'seo_description' => Str::limit('Bu içerik örnek (seed) amaçlı eklenmiştir. Admin panelden güncelleyebilirsiniz.', 155),
                    'published_at' => now(),
                ],
            );
        }

        $faqs = [
            [
                'question' => 'Speedia gibi bir servis sağlayıcı mısınız?',
                'answer' => 'Hayır. Neustar bir karşılaştırma platformudur; abonelik işlemleri ilgili servis sağlayıcı üzerinden yapılır.',
            ],
            [
                'question' => 'Taahhütsüz paketlerde fiyat sonradan artar mı?',
                'answer' => 'Taahhütsüz paketlerde fiyatlar dönemsel olarak güncellenebilir. Karar vermeden önce güncel fiyatları kontrol etmeni öneririz.',
            ],
            [
                'question' => 'Fiber ile VDSL arasındaki fark nedir?',
                'answer' => 'Fiber genellikle daha yüksek hız ve daha stabil bağlantı sunar; VDSL ise çoğunlukla bakır hat üzerinden daha düşük üst sınırlara sahiptir.',
            ],
            [
                'question' => 'Kaç Mbps bana yeter?',
                'answer' => 'Temel kullanım için 16–24 Mbps, çoklu cihaz için 50 Mbps, yoğun kullanım için 100 Mbps+ önerilir.',
            ],
        ];

        foreach ($faqs as $i => $f) {
            Faq::firstOrCreate(
                ['question' => $f['question']],
                [
                    'answer' => $f['answer'],
                    'page_type' => 'home',
                    'relation_id' => null,
                    'is_active' => true,
                    'order' => $i,
                ],
            );
        }
    }
}

