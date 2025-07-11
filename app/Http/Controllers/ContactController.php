<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Contact::orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'message' => 'nullable'
        ]);

        $contact = Contact::create($validated);

        $contactTimestamp = Carbon::now()->toString();

        $from = env('CONTACT_FROM');
        $sgResponse = AppServiceProvider::sendEmail(
            from: $from,
            fromName: 'Website Contact',
            subject: 'Website Contact - ' . $contactTimestamp,
            replyToName: $validated['first_name'] . ' ' . $validated['last_name'],
            replyToEmail: $validated['email'],
            message: $validated['message']
        );

        $contact->sendgrid_response = $sgResponse;
        $contact->save();

        return response()->json(['status' => $sgResponse['statusCode']]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $rec = Contact::find($id);
        $rec->replied = $request->replied;
        $rec->status = $request->status;
        $rec->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rec = Contact::find($id);
        $rec->delete();
        return response()->json(['status' => 'ok']);
    }
}
