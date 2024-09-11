<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('borrow_details', function (Blueprint $table) {
        $table->string('story_borrower')->nullable();
    });
}

public function down()
{
    Schema::table('borrow_details', function (Blueprint $table) {
        $table->dropColumn('story_borrower');
    });
}

};
