<?php

use App\Models\BiblioType;
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
        Schema::create('biblios', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BiblioType::class)->constrained()->onDelete('cascade');
            $table->string('title');
            $table->date('sort_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblios');
    }
};
