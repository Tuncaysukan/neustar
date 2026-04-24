<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfrastructureLead;
use Illuminate\Http\Request;

class InfrastructureLeadController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $leads = InfrastructureLead::query()
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
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $counts = InfrastructureLead::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.infrastructure-leads.index', [
            'leads'   => $leads,
            'q'       => $q,
            'status'  => $status,
            'counts'  => $counts,
            'statusLabels' => InfrastructureLead::STATUS_LABELS,
        ]);
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
}
