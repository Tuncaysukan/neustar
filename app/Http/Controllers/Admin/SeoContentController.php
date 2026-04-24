<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoContent;
use Illuminate\Http\Request;

class SeoContentController extends Controller
{
    public function index()
    {
        $seoContents = SeoContent::latest()->paginate(10);
        return view('admin.seo.index', compact('seoContents'));
    }

    public function create()
    {
        return view('admin.seo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_key' => 'required|unique:seo_contents,page_key',
            'title' => 'nullable|max:255',
            'content' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
        ]);

        SeoContent::create($validated);

        return redirect()->route('admin.seo.index')->with('success', 'SEO içeriği başarıyla eklendi.');
    }

    public function edit(SeoContent $seo)
    {
        return view('admin.seo.edit', compact('seo'));
    }

    public function update(Request $request, SeoContent $seo)
    {
        $validated = $request->validate([
            'page_key' => 'required|unique:seo_contents,page_key,' . $seo->id,
            'title' => 'nullable|max:255',
            'content' => 'nullable',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
        ]);

        $seo->update($validated);

        return redirect()->route('admin.seo.index')->with('success', 'SEO içeriği başarıyla güncellendi.');
    }

    public function destroy(SeoContent $seo)
    {
        $seo->delete();
        return redirect()->route('admin.seo.index')->with('success', 'SEO içeriği başarıyla silindi.');
    }
}
