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
        Schema::create('boat_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boat_id')->constrained();
            $table->string('status');  // 'ACTIVE' or 'INACTIVE'
            $table->date('date');  // The date this status was valid for
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boat_status_logs');
    }
};
