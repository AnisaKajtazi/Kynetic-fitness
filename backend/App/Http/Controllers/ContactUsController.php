<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:client,staff',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:50',
            'comment' => 'required|string',
        ]);

        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        ContactUs::create($data);

        return response()->json(['message' => 'Message sent successfully!']);
    }
}
