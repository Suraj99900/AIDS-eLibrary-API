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
        Schema::table('aids_staff_upload', function (Blueprint $table) {
            $table->date("submission_date")->after("file_type")->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aids_staff_upload', function (Blueprint $table) {
            $table->dropColumn("submission_date");
        });
    }
};
