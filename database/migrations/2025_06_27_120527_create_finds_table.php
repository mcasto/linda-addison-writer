<?php

use App\Models\FindType;
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
        Schema::create('finds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FindType::class)->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('url');
            $table->date('date');
            $table->string('md_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finds');
    }
};
