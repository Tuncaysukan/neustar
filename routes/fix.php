<?php

use Illuminate\Support\Facades\Route;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

Route::get('/fix-blogs', function () {
    $genelCat = BlogCategory::firstOrCreate(
        ['slug' => 'genel'],
        ['name' => 'Genel', 'is_active' => true]
    );

    $blogs = Blog::all();
    $count = 0;
    foreach ($blogs as $blog) {
        $blog->blog_category_id = $genelCat->id;
        $blog->is_active = true;
        if (!$blog->published_at) {
            $blog->published_at = now();
        }
        $blog->save();
        $count++;
    }

    return "Fixed $count blogs and assigned to 'Genel' category.";
});
