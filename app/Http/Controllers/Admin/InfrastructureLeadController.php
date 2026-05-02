<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureLead;
use App\Models\Operator;
use Illuminate\Http\Request;

class InfrastructureLeadController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $status   = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');
        $operatorId = $request->query('operator_id');

        $query = $this->buildQuery($q, $status, $dateFrom, $dateTo, $operatorId);

        $leads = $query->latest('id')->paginate(25)->withQueryString();

        $counts = InfrastructureLead::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Operatör listesi (filtre için)
        $operators = \App\Models\Operator::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.infrastructure-leads.index', [
            'leads'        => $leads,
            'q'            => $q,
            'status'       => $status,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'operators'    => $operators,
            'counts'       => $counts,
            'statusLabels' => InfrastructureLead::STATUS_LABELS,
        ]);
    }

    /**
     * Excel (CSV) export — mevcut filtreleri uygular.
     */
    public function export(Request $request)
    {
        $q          = trim((string) $request->query('q', ''));
        $status     = $request->query('status');
        $dateFrom   = $request->query('date_from');
        $dateTo     = $request->query('date_to');
        $operatorId = $request->query('operator_id');

        $leads = $this->buildQuery($q, $status, $dateFrom, $dateTo, $operatorId)
            ->latest('id')
            ->get();

        $filename = 'basvurular-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($leads) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM — Excel Türkçe karakterleri doğru okusun
            fputs($handle, "\xEF\xBB\xBF");

            // Başlık satırı
            fputcsv($handle, [
                'ID', 'Ad Soyad', 'Telefon', 'E-posta',
                'İl', 'İlçe', 'Mahalle', 'Sokak', 'Bina No',
                'Durum', 'Tarih',
            ], ';');

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->full_name,
                    $lead->phone,
                    $lead->email ?? '',
                    $lead->city_name ?? '',
                    $lead->district_name ?? '',
                    $lead->neighborhood_name ?? '',
                    $lead->street ?? '',
                    $lead->building_no ?? '',
                    $lead->status_label ?? $lead->status,
                    $lead->created_at->format('d.m.Y H:i'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(InfrastructureLead $lead)
    {
        return view('admin.infrastructure-leads.show', [
            'lead'         => $lead,
            'statusLabels' => InfrastructureLead::STATUS_LABELS,
        ]);
    }

    public function update(Request $request, InfrastructureLead $lead)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:' . implode(',', array_keys(InfrastructureLead::STATUS_LABELS))],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead->update($data);

        return back()->with('status', 'Başvuru güncellendi.');
    }

    public function destroy(InfrastructureLead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('admin.infrastructure-leads.index')
            ->with('status', 'Başvuru silindi.');
    }

    // ── Ortak sorgu builder ──────────────────────────────────────────
    private function buildQuery(string $q, ?string $status, ?string $dateFrom, ?string $dateTo, ?string $operatorId)
    {
        return InfrastructureLead::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $like = '%' . $q . '%';
                    $inner->where('full_name', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('city_name', 'like', $like)
                        ->orWhere('district_name', 'like', $like)
                        ->orWhere('neighborhood_name', 'like', $like);
                });
            })
            ->when(in_array($status, array_keys(InfrastructureLead::STATUS_LABELS), true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo));
    }
}
