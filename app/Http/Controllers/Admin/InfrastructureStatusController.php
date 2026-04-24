<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InfrastructureStatusController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = InfrastructureStatus::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('city_name', 'like', $like)
                      ->orWhere('district_name', 'like', $like)
                      ->orWhere('neighborhood_name', 'like', $like)
                      ->orWhere('city_slug', 'like', $like);
                });
            })
            ->orderBy('city_name')
            ->orderByRaw('district_slug IS NULL DESC')
            ->orderBy('district_name')
            ->orderByRaw('neighborhood_slug IS NULL DESC')
            ->paginate(20)
            ->withQueryString();

        return view('admin.infrastructure.index', compact('rows', 'q'));
    }

    public function create()
    {
        $record = new InfrastructureStatus();
        return view('admin.infrastructure.create', compact('record'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->withSlugs($data);

        InfrastructureStatus::create($data);

        return redirect()
            ->route('admin.infrastructure.index')
            ->with('success', 'Altyapı kaydı eklendi.');
    }

    public function edit(InfrastructureStatus $infrastructure)
    {
        $record = $infrastructure;
        return view('admin.infrastructure.edit', compact('record'));
    }

    public function update(Request $request, InfrastructureStatus $infrastructure)
    {
        $data = $this->validated($request, $infrastructure->id);
        $data = $this->withSlugs($data);

        $infrastructure->update($data);

        return redirect()
            ->route('admin.infrastructure.index')
            ->with('success', 'Altyapı kaydı güncellendi.');
    }

    public function destroy(InfrastructureStatus $infrastructure)
    {
        $infrastructure->delete();

        return redirect()
            ->route('admin.infrastructure.index')
            ->with('success', 'Kayıt silindi.');
    }

    // ---------------------------------------------------------------

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'city_name'         => ['required', 'string', 'max:96'],
            'district_name'     => ['nullable', 'string', 'max:96'],
            'neighborhood_name' => ['nullable', 'string', 'max:128'],
            'fiber_coverage'    => ['nullable', 'integer', 'between:0,100'],
            'vdsl_coverage'     => ['nullable', 'integer', 'between:0,100'],
            'adsl_coverage'     => ['nullable', 'integer', 'between:0,100'],
            'max_down_mbps'     => ['nullable', 'integer', 'between:0,10000'],
            'max_up_mbps'       => ['nullable', 'integer', 'between:0,10000'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function withSlugs(array $data): array
    {
        $data['city_slug']         = Str::slug($data['city_name'] ?? '');
        $data['district_slug']     = ($data['district_name'] ?? null)
            ? Str::slug($data['district_name']) : null;
        $data['neighborhood_slug'] = ($data['neighborhood_name'] ?? null)
            ? Str::slug($data['neighborhood_name']) : null;

        return $data;
    }
}
