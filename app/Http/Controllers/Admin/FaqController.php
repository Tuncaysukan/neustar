<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Operator;
use App\Models\InternetPackage;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order')->paginate(10);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $operators = Operator::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $packages = InternetPackage::with('operator')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'operator_id']);
        return view('admin.faqs.create', compact('operators', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|max:255',
            'answer' => 'required',
            'page_type' => 'required|string',
            'relation_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'SSS başarıyla eklendi.');
    }

    public function edit(Faq $faq)
    {
        $operators = Operator::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $packages = InternetPackage::with('operator')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'operator_id']);
        return view('admin.faqs.edit', compact('faq', 'operators', 'packages'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|max:255',
            'answer' => 'required',
            'page_type' => 'required|string',
            'relation_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'SSS başarıyla güncellendi.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'SSS başarıyla silindi.');
    }
}
