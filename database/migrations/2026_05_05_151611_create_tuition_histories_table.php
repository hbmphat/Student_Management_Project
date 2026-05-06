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
        Schema::create('tuition_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuition_id')->constrained('tuitions')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');

            $table->enum('action_type', ['extend', 'compensate', 'manual_edit']);
            $table->integer('days_added')->default(0); // Số ngày cộng thêm

            $table->date('old_to_date')->nullable();
            $table->date('new_to_date');

            $table->string('note')->nullable(); // Lý do bù/sửa
            $table->foreignId('created_by')->constrained('users'); // Giáo vụ thao tác
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuition_histories');
    }
};
