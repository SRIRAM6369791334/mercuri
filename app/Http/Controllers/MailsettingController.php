<?php

namespace App\Http\Controllers;

use App\Models\Mailsetting;
use Illuminate\Http\Request;

class MailsettingController extends Controller
{
    public function edit($id = null)
    {
        $mailsetting = Mailsetting::first();
        return view('pages.mailsettings', compact('mailsetting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'from_email' => 'required|email',
            'cc_email.*' => 'nullable|email'
        ]);

        $ccEmails = array_filter($request->cc_email ?? []);

        Mailsetting::updateOrCreate(
            ['id' => 1],
            [
                'from_email' => $request->from_email,
                'cc_email'   => implode(',', $ccEmails),
            ]
        );

        return back()->with('success', 'Mail settings updated successfully');
    }
}
