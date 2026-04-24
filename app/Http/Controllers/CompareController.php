<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $packageIds = explode(',', $request->ids);
        $packages = InternetPackage::with('operator')
            ->whereIn('id', $packageIds)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        return view('frontend.compare.index', compact('packages'));
    }
}
