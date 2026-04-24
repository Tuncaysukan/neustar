<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternetPackage;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InternetPackageController extends Controller
{
    public function index()
    {
        $packages = InternetPackage::with('operator')->latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
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
