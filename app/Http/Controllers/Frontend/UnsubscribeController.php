<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    public function unsubscribe(string $token, ContactService $contactService)
    {
        $email = base64_decode($token);

        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return view('frontend.unsubscribed', ['success' => false]);
        }

        $contact = Contact::where('email', $email)->first();

        if (! $contact) {
            return view('frontend.unsubscribed', ['success' => false]);
        }

        $contactService->unsubscribe($contact);

        return view('frontend.unsubscribed', ['success' => true, 'email' => $email]);
    }
}
