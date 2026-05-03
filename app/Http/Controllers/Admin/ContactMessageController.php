<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $q        = trim((string) $request->query('q', ''));
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        $query = ContactMessage::query()->latest('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('subject', 'like', '%' . $q . '%')
                    ->orWhere('message', 'like', '%' . $q . '%');
            });
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $messages = $query->paginate(25)->withQueryString();

        return view('admin.contact-messages.index', [
            'messages' => $messages,
            'q'        => $q,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
        ]);
    }

    public function show(ContactMessage $contact_message): View
    {
        return view('admin.contact-messages.show', ['message' => $contact_message]);
    }

    public function destroy(ContactMessage $contact_message): RedirectResponse
    {
        $contact_message->delete();

        return redirect()
            ->route('admin.contact-messages.index')
            ->with('success', 'Mesaj silindi.');
    }
}
