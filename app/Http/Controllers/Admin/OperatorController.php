<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OperatorController extends Controller
{
    public function index()
    {
        $operators = Operator::latest()->paginate(10);
        return view('admin.operators.index', compact('operators'));
    }

    public function create()
    {
        return view('admin.operators.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        $validated['slug'] = Str::slug($request->name);
        $validated['logo'] = $this->handleLogoUpload($request);

        Operator::create($validated);

        return redirect()
            ->route('admin.operators.index')
            ->with('success', 'Operatör başarıyla eklendi.');
    }

    public function edit(Operator $operator)
    {
        return view('admin.operators.edit', compact('operator'));
    }

    public function update(Request $request, Operator $operator)
    {
        $validated = $this->validatePayload($request);
        $validated['slug'] = Str::slug($request->name);

        if ($request->boolean('remove_logo')) {
            $this->deleteStoredLogo($operator->logo);
            $validated['logo'] = null;
        }

        if ($request->hasFile('logo_file')) {
            $this->deleteStoredLogo($operator->logo);
            $validated['logo'] = $this->handleLogoUpload($request);
        }

        $operator->update($validated);

        return redirect()
            ->route('admin.operators.index')
            ->with('success', 'Operatör başarıyla güncellendi.');
    }

    public function destroy(Operator $operator)
    {
        $this->deleteStoredLogo($operator->logo);
        $operator->delete();

        return redirect()
            ->route('admin.operators.index')
            ->with('success', 'Operatör başarıyla silindi.');
    }

    /* -------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------*/
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'website_url'      => 'nullable|url|max:255',
            'description'      => 'nullable|string',
            'seo_title'        => 'nullable|string|max:255',
            'seo_description'  => 'nullable|string|max:500',
            'seo_text'         => 'nullable|string',
            'is_active'        => 'nullable|boolean',
            'logo_file'        => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:512',
        ]);
    }

    /**
     * Stores an uploaded logo into the public disk under `operators/` and
     * returns the relative path (e.g. operators/slug-1710000000.png). Returns
     * null when no file is provided.
     */
    private function handleLogoUpload(Request $request): ?string
    {
        if (! $request->hasFile('logo_file')) {
            return null;
        }
        $file = $request->file('logo_file');
        $base = Str::slug($request->input('name', 'brand')) ?: 'brand';
        $name = $base . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('operators', $name, 'public');
    }

    private function deleteStoredLogo(?string $path): void
    {
        if (! $path) return;
        if (Str::startsWith($path, ['http://', 'https://'])) return;
        Storage::disk('public')->delete($path);
    }
}
