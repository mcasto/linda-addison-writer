<?php

namespace App\Http\Controllers;

use App\Mail\ContactMailer;
use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'email' => 'email|required',
            'message' => 'string|required'
        ]);

        if ($validator->fails()) {
            return ['status' => 'error', 'message' => 'Invalid contact information'];
        }

        $contact = Contact::create($validator->valid());

        // send email about contact
        if (config('app.env') == 'production') {
            try {
                Mail::to(config('mail.to'))
                    ->send(new ContactMailer($contact));
            } catch (Exception $e) {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return ['status' => 'success'];
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
