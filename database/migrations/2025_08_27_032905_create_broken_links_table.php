<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('broken_links', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // e.g., 'covers', 'events', 'finds', etc.
            $table->unsignedBigInteger('table_id'); // The ID from the referenced table
            $table->boolean('confirmed_broken')->default(false); // Only true when admin confirms
            $table->boolean('confirmed_working')->default(false); // Only true when admin confirms
            $table->date('confirmed_date')->nullable(); // Date of manual confirmation
            $table->timestamps();

            // Composite index for the polymorphic relationship
            $table->index(['table_name', 'table_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('broken_links');
    }
};
