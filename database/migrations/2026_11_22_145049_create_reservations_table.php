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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('status')->default('confirmed')->index();

            $table->timestamps();

            $table->unique(['workshop_id','attendee_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
