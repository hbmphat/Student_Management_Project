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
        Schema::create('tuitions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
        $table->date('from_date')->nullable();
        $table->date('to_date')->nullable();
        $table->timestamps();
        
        // Đảm bảo 1 học viên trong 1 lớp chỉ có 1 bản ghi hạn học phí
        $table->unique(['student_id', 'class_room_id']); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuitions');
    }
};
