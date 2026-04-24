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
        $faqs = Faq::where('is_active', true)
            ->where(function ($query) {
                $query->where('page_type', 'speed_test')
                      ->orWhere('page_type', 'general');
            })
            ->orderBy('order')
            ->get();
        
        return view('frontend.tools.speed-test', compact('faqs'));
    }

    public function commitmentCounter()
    {
        $faqs = Faq::where('is_active', true)
            ->where(function ($query) {
                $query->where('page_type', 'commitment')
                      ->orWhere('page_type', 'general');
            })
            ->orderBy('order')
            ->get();
        
        return view('frontend.tools.commitment-counter', compact('faqs'));
    }

    public function commitmentReminderStore(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'email'      => ['nullable', 'email', 'max:160'],
            'phone'      => ['nullable', 'string', 'max:32', 'regex:/^[0-9 +()\-]{7,}$/'],
            'start_date' => ['required', 'date'],
            'months'     => ['required', 'integer', 'in:12,24'],
            'end_date'   => ['required', 'date'],
            'remaining_days' => ['required', 'integer', 'min:0'],
            'kvkk'       => ['required', 'accepted'],
        ]);

        // En az biri zorunlu
        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json([
                'ok'      => false,
                'message' => 'E-posta veya telefon numarasından en az birini girin.',
            ], 422);
        }

        \App\Models\CommitmentReminder::create([
            'email'          => $data['email'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'start_date'     => $data['start_date'],
            'months'         => $data['months'],
            'end_date'       => $data['end_date'],
            'remaining_days' => $data['remaining_days'],
            'ip'             => $request->ip(),
            'kvkk_approved_at' => now(),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Kaydedildi! Taahhüt bitimine yakın sizi bilgilendireceğiz.',
        ]);
    }

    public function operators()
    {
        $operators = Operator::where('is_active', true)->paginate(12);
        return view('frontend.operators.index', compact('operators'));
    }

    public function operatorDetail($slug)
    {
        $operator = Operator::with('packages')->where('slug', $slug)->firstOrFail();
        
        $faqs = Faq::where('is_active', true)
            ->where(function ($query) use ($operator) {
                $query->where('page_type', 'operator')
                      ->where('relation_id', $operator->id)
                      ->orWhere('page_type', 'general');
            })
            ->orderBy('order')
            ->get();
        
        return view('frontend.operators.show', compact('operator', 'faqs'));
    }
}
