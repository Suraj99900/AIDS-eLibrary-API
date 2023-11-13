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
        Schema::create('aids_book_issue_book', function (Blueprint $table) {
            $table->id()->index();
            $table->string('zprn')->index();
            $table->integer('book_id')->index();
            $table->date('issue_date')->index();
            $table->date('return_date')->index()->nullable();
            $table->integer('is_return')->default(0);
            $table->string('user_name')->nullable();
            $table->integer('status')->index()->default(1);
            $table->integer('deleted')->index()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aids_book_issue_book');
    }
};
