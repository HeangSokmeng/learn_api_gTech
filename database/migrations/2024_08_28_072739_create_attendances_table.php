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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->enum('checkin_status', ['late', 'early']);
            $table->enum('checkout_status', ['late', 'early'])->nullable();
            $table->enum('attendances_status', ['A', 'P']);
            $table->timestamps();

            $table->foreign('staff_id')
                  ->references('id')
                  ->on('staff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
