<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $featured = Blog::published()
            ->with('categoryRel')
            ->latest('published_at')
            ->latest('created_at')
            ->first();

        $blogsQuery = Blog::published()
            ->with('categoryRel')
            ->search($search)
            ->latest('published_at')
            ->latest('created_at');

        if ($featured && ! $search) {
            $blogsQuery->where('id', '!=', $featured->id);
        }

        $blogs = $blogsQuery->paginate(9)->withQueryString();

        $categories = \App\Models\BlogCategory::where('is_active', true)->orderBy('name')->get();

        return view('frontend.blog.index', [
            'featured'   => $featured,
            'blogs'      => $blogs,
            'categories' => $categories,
            'search'     => $search,
            'category'   => null,
        ]);
    }

    public function categoryIndex(string $categorySlug)
    {
        $category = \App\Models\BlogCategory::where('slug', $categorySlug)->where('is_active', true)->firstOrFail();
        
        $blogs = Blog::published()
            ->with('categoryRel')
            ->where('blog_category_id', $category->id)
            ->latest('published_at')
            ->latest('created_at')
            ->paginate(12);

        $categories = \App\Models\BlogCategory::where('is_active', true)->orderBy('name')->get();

        return view('frontend.blog.index', [
            'blogs'      => $blogs,
            'category'   => $category,
            'categories' => $categories,
            'featured'   => null,
            'search'     => null,
        ]);
    }

    public function show(string $categorySlug, string $slug)
    {
        $category = \App\Models\BlogCategory::where('slug', $categorySlug)->firstOrFail();
        $blog = Blog::published()
            ->with('categoryRel')
            ->where('slug', $slug)
            ->where('blog_category_id', $category->id)
            ->firstOrFail();

        // Basit görüntülenme sayacı — aynı oturumda tekrar saymaz.
        $sessionKey = "blog_viewed_{$blog->id}";
        if (! session()->has($sessionKey)) {
            $blog->increment('views');
            session()->put($sessionKey, true);
        }

        $related = Blog::published()
            ->with('categoryRel')
            ->where('id', '!=', $blog->id)
            ->where('blog_category_id', $blog->blog_category_id)
            ->latest('published_at')
            ->latest('created_at')
            ->limit(3)
            ->get();

        // Yeterli ilgili yazı yoksa son yazılarla doldur.
        if ($related->count() < 3) {
            $related = $related->merge(
                Blog::published()
                    ->with('categoryRel')
                    ->where('id', '!=', $blog->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->latest('published_at')
                    ->latest('created_at')
                    ->limit(3 - $related->count())
                    ->get()
            );
        }

        return view('frontend.blog.show', compact('blog', 'related'));
    }
}
