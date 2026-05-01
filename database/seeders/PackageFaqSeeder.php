<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\InternetPackage;
use Illuminate\Database\Seeder;

class PackageFaqSeeder extends Seeder
{
    public function run(): void
    {
        $pkg = InternetPackage::where('slug', 'digiturk-100-mbps-internet-ilk-3-ay-indirimli-kampanya')->first();

        if (! $pkg) {
            $this->command->warn('Paket bulunamadı.');
            return;
        }

        $faqs = [
            [
                'question'    => 'Bu paket hangi altyapıyı kullanıyor?',
                'answer'      => 'Digiturk 100 Mbps paketi Fiber altyapı üzerinden hizmet vermektedir. Adresinizde fiber altyapı olup olmadığını altyapı sorgulama aracımızla kontrol edebilirsiniz.',
                'page_type'   => 'package',
                'relation_id' => $pkg->id,
                'is_active'   => true,
                'order'       => 1,
            ],
            [
                'question'    => 'Taahhüt süresi dolmadan iptal edersem ne olur?',
                'answer'      => '12 aylık taahhüt süresinde erken iptal durumunda kalan ay sayısı üzerinden cayma bedeli uygulanabilir. Kesin bilgi için Digiturk müşteri hizmetlerini aramanızı öneririz.',
                'page_type'   => 'package',
                'relation_id' => $pkg->id,
                'is_active'   => true,
                'order'       => 2,
            ],
            [
                'question'    => 'Kurulum ücreti var mı?',
                'answer'      => 'Kampanya kapsamında kurulum ücreti alınmamaktadır. Ancak modem bedeli ayrıca değerlendirilebilir. Güncel kampanya detayları için operatörün resmi sitesini ziyaret edin.',
                'page_type'   => 'package',
                'relation_id' => $pkg->id,
                'is_active'   => true,
                'order'       => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question'], 'relation_id' => $faq['relation_id']],
                $faq
            );
        }

        $this->command->info("✓ {$pkg->name} için " . count($faqs) . " SSS eklendi.");
    }
}
