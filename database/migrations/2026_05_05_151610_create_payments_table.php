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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->onDelete('set null');

            $table->string('receipt_code')->unique(); // Mã biên lai

            // SỬA Ở ĐÂY: Lưu thẳng số tuần họ đóng (8, 16, 32...) thay vì lưu gói
            $table->integer('paid_weeks');

            $table->decimal('original_amount', 15, 2); // Giá gốc (Bằng giá 1 tuần của Khóa * paid_weeks)
            $table->decimal('discount_amount', 15, 2)->default(0); // Tiền giảm
            $table->decimal('final_amount', 15, 2); // Thực thu

            $table->enum('payment_method', ['cash', 'vietqr']);
            $table->boolean('is_used')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
