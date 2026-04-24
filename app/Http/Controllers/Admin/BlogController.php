<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog yazısı başarıyla eklendi.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $this->validated($request);

        if ($data['title'] !== $blog->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $blog->id);
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog yazısı başarıyla güncellendi.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog yazısı başarıyla silindi.');
    }

    /* --------------------------------------------------------- */

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title'           => 'required|max:255',
            'content'         => 'required',
            'excerpt'         => 'nullable|string|max:500',
            'category'        => 'nullable|string|max:80',
            'image'           => 'nullable|string|max:500',
            'is_active'       => 'boolean',
            'published_at'    => 'nullable|date',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (Blog::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
