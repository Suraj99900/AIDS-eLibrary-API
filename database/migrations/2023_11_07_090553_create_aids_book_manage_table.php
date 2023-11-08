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
        Schema::create('aids_book_manage', function (Blueprint $table) {
            $table->id();
            $table->string('book_name')->index();
            $table->string('isbn_no')->index();
            $table->timestamp('added_on');
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
        Schema::dropIfExists('aids_book_manage');
    }
};
