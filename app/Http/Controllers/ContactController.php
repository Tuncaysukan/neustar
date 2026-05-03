<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('frontend.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:160'],
            'subject' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
            'kvkk'    => ['required', 'accepted'],
        ]);

        $data = collect($validated)->except('kvkk')->all();

        ContactMessage::create(array_merge($data, [
            'ip'               => $request->ip(),
            'kvkk_approved_at' => now(),
        ]));

        return back()->with('status', 'Mesajın alındı. En kısa sürede dönüş yapacağız.');
    }
}

