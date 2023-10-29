<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aids_student_semester', function (Blueprint $table) {
            $table->id()->index();
            $table->string("semester");
            $table->boolean("status")->default(1);
            $table->boolean("deleted")->default(0);
            $table->timestamps();
        });
        $semesters = ['First Semester', 'Second Semester', 'Third Semester', 'Fourth Semester', 'Fifth Semester', 'Sixth Semester', 'Seventh Semester', 'Eighth Semester'];

        foreach ($semesters as $semester) {
            DB::table('aids_student_semester')->insert([
                [
                    'semester' => $semester,
                    'status' => 1,
                    'deleted' => 0,
                    'created_at' => date("Y-m-d h:i:s"),
                    'updated_at' => date("Y-m-d h:i:s"),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aids_student_semester');
    }
};
