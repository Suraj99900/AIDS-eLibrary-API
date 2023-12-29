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
        Schema::create('subfolder', function (Blueprint $table) {
            $table->id();
            $table->integer("master_folder_id");
            $table->string("sub_folder");
            $table->string('user_name')->nullable();
            $table->date('creation_on')->nullable()->index();
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
        Schema::dropIfExists('subfolder');
    }
};
