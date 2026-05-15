<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactLeadController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $leads = ContactLead::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contact-leads.index', [
            'leads' => $leads,
            'status' => $status,
        ]);
    }
}
