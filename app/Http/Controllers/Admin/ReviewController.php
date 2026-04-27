<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PackageReview::with('package.operator')->latest();

        if ($request->query('status') === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->query('status') === 'approved') {
            $query->where('is_approved', true);
        }

        $reviews = $query->paginate(20)->withQueryString();
        $pendingCount = PackageReview::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve(PackageReview $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Yorum onaylandı.');
    }

    public function reject(PackageReview $review)
    {
        $review->delete();
        return back()->with('success', 'Yorum silindi.');
    }

    public function bulkApprove(Request $request)
    {
        PackageReview::where('is_approved', false)->update(['is_approved' => true]);
        return back()->with('success', 'Tüm bekleyen yorumlar onaylandı.');
    }
}
