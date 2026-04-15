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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration_months'); // Thời lượng (tháng)
            $table->decimal('weekly_price', 15, 2); // Giá tiền mỗi tuần
            $table->text('description')->nullable(); // Mô tả khóa học
            $table->timestamps();
            $table->softDeletes(); // Cực kỳ quan trọng để không mất file lịch sử
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
