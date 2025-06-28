<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('contacts');

        foreach ($recs as $rec) {
            $created = date("Y-m-d H:i:s", $rec['received']);

            Contact::create([
                'first_name' => $rec['firstName'],
                'last_name' => $rec['lastName'],
                'email' => $rec['email'],
                'message' => $rec['message'],
                'status' => $rec['status'],
                'replied' => $rec['replied'],
                'created_at' => $created
            ]);
        }
    }
}
