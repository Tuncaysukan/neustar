<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Ev İnterneti',
            'Altyapı Rehberleri',
            'Operatör Haberleri',
            'Teknoloji',
        ];

        foreach ($categories as $catName) {
            $category = BlogCategory::updateOrCreate(
                ['slug' => Str::slug($catName)],
                ['name' => $catName, 'is_active' => true]
            );

            // Her kategoriye 3-4 yazı ekle
            for ($i = 1; $i <= 3; $i++) {
                $title = $catName . ' Hakkında Bilmeniz Gerekenler ' . $i;
                Blog::updateOrCreate(
                    ['slug' => Str::slug($title)],
                    [
                        'blog_category_id' => $category->id,
                        'title' => $title,
                        'content' => 'Bu bir örnek blog içeriğidir. ' . $catName . ' dünyasındaki son gelişmeleri burada bulabilirsiniz. İnternet hızınızı artırmak ve en iyi tarifeyi seçmek için rehberlerimizi takip edin.',
                        'is_active' => true,
                        'published_at' => now()->subDays(rand(1, 30)),
                        'views' => rand(100, 1000),
                    ]
                );
            }
        }
    }
}
