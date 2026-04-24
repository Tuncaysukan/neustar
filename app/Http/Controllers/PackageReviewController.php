<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\PackageReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageReviewController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $package = InternetPackage::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        PackageReview::create([
            'internet_package_id' => $package->id,
            'name' => $validated['name'],
            'rating' => $validated['rating'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => true,
        ]);

        return back()->with('status', 'Yorumun eklendi. Teşekkürler!');
    }
}

