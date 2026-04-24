<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');
        $search   = $request->query('q');

        $featured = Blog::published()
            ->category($category)
            ->latest('published_at')
            ->latest('created_at')
            ->first();

        $blogsQuery = Blog::published()
            ->category($category)
            ->search($search)
            ->latest('published_at')
            ->latest('created_at');

        if ($featured && ! $search) {
            $blogsQuery->where('id', '!=', $featured->id);
        }

        $blogs = $blogsQuery->paginate(9)->withQueryString();

        $categories = Blog::published()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('frontend.blog.index', compact('featured', 'blogs', 'categories', 'category', 'search'));
    }

    public function show(string $slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        // Basit görüntülenme sayacı — aynı oturumda tekrar saymaz.
        $sessionKey = "blog_viewed_{$blog->id}";
        if (! session()->has($sessionKey)) {
            $blog->increment('views');
            session()->put($sessionKey, true);
        }

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->when($blog->category, fn ($q) => $q->where('category', $blog->category))
            ->latest('published_at')
            ->latest('created_at')
            ->limit(3)
            ->get();

        // Yeterli ilgili yazı yoksa son yazılarla doldur.
        if ($related->count() < 3) {
            $related = $related->merge(
                Blog::published()
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
