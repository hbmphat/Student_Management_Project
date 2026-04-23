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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->comment('Mã định danh HV000001');
            $table->string('name');
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->string('street_address')->nullable()->comment('Số nhà, tên đường');
            $table->foreignId('ward_id')->nullable()->constrained('wards')->onDelete('set null');
            $table->string('avatar')->nullable();
            $table->string('face_image')->nullable();
            $table->enum('status', ['studying', 'dropped', 'reserved'])->default('studying');
            $table->timestamps();
            $table->softDeletes(); // Bắt buộc cho tính năng Thùng rác
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
