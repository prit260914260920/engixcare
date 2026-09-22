<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        return response()->json(['message' => 'Message submitted successfully.']);
    }

    public function index()
    {
        $messages = ContactMessage::orderByDesc('created_at')->paginate(15);
        return view('admin.inquiries.index', compact('messages'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }
}
