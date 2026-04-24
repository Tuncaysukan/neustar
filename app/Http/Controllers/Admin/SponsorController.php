<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::orderBy('order')->paginate(10);
        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'link' => 'nullable|url',
            'position' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        Sponsor::create($validated);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor başarıyla eklendi.');
    }

    public function edit(Sponsor $sponsor)
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'link' => 'nullable|url',
            'position' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $sponsor->update($validated);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor başarıyla güncellendi.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor başarıyla silindi.');
    }
}
