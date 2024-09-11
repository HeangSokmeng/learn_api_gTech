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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained('user_threes')->onDelete('cascade');
            $table->foreignId('librarian_id')->constrained('user_threes')->onDelete('cascade');

            // Dates
            $table->date('borrow_date');
            $table->date('expect_return_date');
            $table->date('return_date')->nullable();

            // Borrow status
            $table->enum('borrow_status', ['Borrow', 'Return']);
            $table->integer('number_of_borrow_books');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
