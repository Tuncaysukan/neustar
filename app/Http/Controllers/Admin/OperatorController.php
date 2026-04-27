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
        $operator->load('logos');
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

        // ── Çoklu logo yükleme ──────────────────────────────────────
        $this->handleMultipleLogos($request, $operator);

        // ── Logo silme ──────────────────────────────────────────────
        if ($request->filled('delete_logo_ids')) {
            $deleteIds = array_filter(explode(',', $request->input('delete_logo_ids')));
            $toDelete  = $operator->logos()->whereIn('id', $deleteIds)->get();
            foreach ($toDelete as $logo) {
                $this->deleteStoredLogo($logo->path);
                $logo->delete();
            }
        }

        // ── Birincil logo güncelle ──────────────────────────────────
        if ($request->filled('primary_logo_id')) {
            $operator->logos()->update(['is_primary' => false]);
            $operator->logos()->where('id', $request->input('primary_logo_id'))->update(['is_primary' => true]);
        }

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
            'name'                    => 'required|string|max:255',
            'website_url'             => 'nullable|url|max:255',
            'description'             => 'nullable|string',
            'seo_title'               => 'nullable|string|max:255',
            'seo_description'         => 'nullable|string|max:500',
            'seo_text'                => 'nullable|string',
            'is_active'               => 'nullable|boolean',
            'logo_file'               => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'extra_logos.*'           => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'extra_logo_labels.*'     => 'nullable|string|max:80',
            'extra_logo_variants.*'   => 'nullable|in:light,dark,favicon,other',
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

    private function handleMultipleLogos(Request $request, Operator $operator): void
    {
        if (! $request->hasFile('extra_logos')) return;

        $files    = $request->file('extra_logos');
        $labels   = $request->input('extra_logo_labels', []);
        $variants = $request->input('extra_logo_variants', []);

        foreach ($files as $i => $file) {
            if (! $file || ! $file->isValid()) continue;

            $base = Str::slug($operator->name) ?: 'brand';
            $name = $base . '-' . now()->format('YmdHis') . '-' . $i . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('operators', $name, 'public');

            \App\Models\OperatorLogo::create([
                'operator_id' => $operator->id,
                'path'        => $path,
                'label'       => $labels[$i] ?? '',
                'variant'     => $variants[$i] ?? 'light',
                'is_primary'  => false,
                'order'       => $operator->logos()->count(),
            ]);
        }
    }
}
