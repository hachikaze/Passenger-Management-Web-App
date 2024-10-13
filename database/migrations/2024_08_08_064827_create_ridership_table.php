<?php

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
        Schema::create('riderships', function (Blueprint $table) {
            $table->id(); 
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('address');
            $table->string('contact_number');
            $table->string('profession');
            $table->integer('age');
            $table->string('gender');
            $table->string('origin');
            $table->string('destination');
            $table->timestamps();
            $table->boolean('is_guest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ridership');
    }
};
