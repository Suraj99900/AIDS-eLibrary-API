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
        Schema::create('folder_file', function (Blueprint $table) {
            $table->id()->index();
            $table->integer("sub_folder_id")->index();
            $table->string("file")->index();
            $table->string("description")->index();
            $table->string('file_name')->index();
            $table->string('file_Path')->index();
            $table->integer('file_type')->index();
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
        Schema::dropIfExists('folder_file');
    }
};
