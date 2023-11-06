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
        Schema::create('aids_student_info', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("zprn");
            $table->string("email")->nullable();
            $table->string("phone_no")->nullable();
            $table->integer("semester")->index();
            $table->string("address")->nullable();
            $table->timestamp('added_on');
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
        Schema::dropIfExists('aids_student_info');
    }
};
