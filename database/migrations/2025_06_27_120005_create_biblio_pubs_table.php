<?php

use App\Models\Biblio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biblio_pubs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Biblio::class)->constrained()->onDelete('cascade');
            $table->date('pub_date');
            $table->string('display_date');
            $table->text('publication');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblio_pubs');
    }
};
