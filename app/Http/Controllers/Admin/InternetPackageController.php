<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternetPackage;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InternetPackageController extends Controller
{
    public function index(Request $request)
    {
        // Filtreler
        $operatorId = $request->query('operator_id');
        $dateFrom   = $request->query('date_from');
        $dateTo     = $request->query('date_to');

        $query = InternetPackage::with('operator')->latest();

        // Operatör filtresi
        if ($operatorId) {
            $query->where('operator_id', $operatorId);
        }

        // Tarih filtresi (oluşturulma tarihi)
        if ($dateFrom) $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)   $query->whereDate('created_at', '<=', $dateTo);

        // Tıklama sayısını tarih aralığına göre hesapla
        if ($dateFrom || $dateTo) {
            $query->withCount(['clicks' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('created_at', '<=', $dateTo);
            }]);
        } else {
            $query->withCount('clicks');
        }

        $packages  = $query->paginate(10)->withQueryString();
        $operators = Operator::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.packages.index', compact('packages', 'operators', 'operatorId', 'dateFrom', 'dateTo'));
    }

    /**
     * Excel (CSV) export — mevcut filtreleri uygular.
     */
    public function export(Request $request)
    {
        $operatorId = $request->query('operator_id');
        $dateFrom   = $request->query('date_from');
        $dateTo     = $request->query('date_to');

        $query = InternetPackage::with('operator')
            ->withCount('clicks')
            ->latest();

        if ($operatorId) $query->where('operator_id', $operatorId);
        if ($dateFrom)   $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)     $query->whereDate('created_at', '<=', $dateTo);

        $packages = $query->get();
        $filename = 'paketler-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($packages) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, [
                'ID', 'Operatör', 'Paket Adı', 'Fiyat (TL)', 'Hız (Mbps)',
                'Taahhüt (Ay)', 'Altyapı', 'Kota', 'Tıklama', 'Sponsor', 'Aktif', 'Oluşturulma',
            ], ';');

            foreach ($packages as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->operator->name ?? '',
                    $p->name,
                    number_format($p->price, 2, ',', '.'),
                    $p->speed,
                    $p->commitment_period > 0 ? $p->commitment_period : 'Yok',
                    $p->infrastructure_type ?? '',
                    $p->quota,
                    $p->clicks_count ?? $p->click_count,
                    $p->is_sponsored ? 'Evet' : 'Hayır',
                    $p->is_active ? 'Aktif' : 'Pasif',
                    $p->created_at->format('d.m.Y'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $operators = Operator::where('is_active', true)->get();
        return view('admin.packages.create', compact('operators'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'operator_id'         => 'required|exists:operators,id',
            'name'                => 'required|max:255',
            'price'               => 'required|numeric',
            'speed'               => 'required|integer',
            'upload_speed'        => 'nullable|integer',
            'quota'               => 'required|max:255',
            'commitment_period'   => 'required|integer',
            'infrastructure_type' => 'nullable|max:255',
            'description'         => 'nullable|string',
            'advantages'          => 'nullable|string',
            'disadvantages'       => 'nullable|string',
            'affiliate_url'       => 'nullable|url|max:500',
            'modem_included'      => 'nullable|in:free,paid',
            'seo_title'           => 'nullable|max:255',
            'seo_description'     => 'nullable|string',
            'is_active'           => 'boolean',
            'is_sponsored'        => 'boolean',
            'apply_type'          => 'required|in:site,call,form',
            'external_url'        => 'nullable|url|max:500',
            'call_number'         => 'nullable|max:32',
        ]);

        $validated['slug'] = Str::slug($request->name . '-' . rand(100, 999));

        InternetPackage::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla eklendi.');
    }

    public function edit(InternetPackage $package)
    {
        $operators = Operator::where('is_active', true)->get();
        return view('admin.packages.edit', compact('package', 'operators'));
    }

    public function update(Request $request, InternetPackage $package)
    {
        $validated = $request->validate([
            'operator_id'         => 'required|exists:operators,id',
            'name'                => 'required|max:255',
            'price'               => 'required|numeric',
            'speed'               => 'required|integer',
            'upload_speed'        => 'nullable|integer',
            'quota'               => 'required|max:255',
            'commitment_period'   => 'required|integer',
            'infrastructure_type' => 'nullable|max:255',
            'description'         => 'nullable|string',
            'advantages'          => 'nullable|string',
            'disadvantages'       => 'nullable|string',
            'affiliate_url'       => 'nullable|url|max:500',
            'modem_included'      => 'nullable|in:free,paid',
            'seo_title'           => 'nullable|max:255',
            'seo_description'     => 'nullable|string',
            'is_active'           => 'boolean',
            'is_sponsored'        => 'boolean',
            'apply_type'          => 'required|in:site,call,form',
            'external_url'        => 'nullable|url|max:500',
            'call_number'         => 'nullable|max:32',
        ]);

        if ($package->name !== $request->name) {
            $validated['slug'] = Str::slug($request->name . '-' . rand(100, 999));
        }

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla güncellendi.');
    }

    public function destroy(InternetPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Paket başarıyla silindi.');
    }
}
