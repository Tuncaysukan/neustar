<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\Sponsor;
use App\Models\Blog;
use App\Models\Faq;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPackages = InternetPackage::with('operator')
            ->where('is_active', true)
            ->where('is_sponsored', true)
            ->limit(6)
            ->get();

        $latestBlogs = Blog::where('is_active', true)
            ->latest()
            ->limit(3)
            ->get();

        $operators = Operator::where('is_active', true)->get();

        $faqs = Faq::where('is_active', true)
            ->whereIn('page_type', ['home', 'general'])
            ->orderBy('order')
            ->limit(8)
            ->get();

        return view('frontend.home', compact('featuredPackages', 'latestBlogs', 'operators', 'faqs'));
    }

    public function speedTest()
    {
        return view('frontend.tools.speed-test');
    }

    public function commitmentCounter()
    {
        return view('frontend.tools.commitment-counter');
    }

    public function operators()
    {
        $operators = Operator::where('is_active', true)->paginate(12);
        return view('frontend.operators.index', compact('operators'));
    }

    public function operatorDetail($slug)
    {
        $operator = Operator::with('packages')->where('slug', $slug)->firstOrFail();
        return view('frontend.operators.show', compact('operator'));
    }
}
