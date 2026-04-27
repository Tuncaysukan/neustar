<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitmentReminder;
use Illuminate\Http\Request;

class CommitmentReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = CommitmentReminder::latest();

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $reminders = $query->paginate(20)->withQueryString();
        $total     = CommitmentReminder::count();
        $expiring  = CommitmentReminder::where('end_date', '<=', now()->addDays(30))
                        ->where('end_date', '>=', now())->count();

        return view('admin.commitment-reminders.index', compact('reminders', 'total', 'expiring'));
    }

    public function destroy(CommitmentReminder $commitmentReminder)
    {
        $commitmentReminder->delete();
        return back()->with('success', 'Hatırlatıcı silindi.');
    }
}
