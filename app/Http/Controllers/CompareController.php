<?php

namespace App\Http\Controllers;

use App\Models\Faq;
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

        $faqs = Faq::where('is_active', true)
            ->where(function ($query) {
                $query->where('page_type', 'compare')
                      ->orWhere('page_type', 'general');
            })
            ->orderBy('order')
            ->get();

        return view('frontend.compare.index', compact('packages', 'faqs'));
    }
}
