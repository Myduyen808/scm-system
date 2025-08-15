<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Khách hàng tạo ticket
            $table->string('subject'); // Chủ đề
            $table->text('description'); // Mô tả vấn đề
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open'); // Trạng thái
            $table->text('reply')->nullable(); // Phản hồi từ nhân viên
            $table->foreignId('employee_id')->nullable()->constrained('users')->onDelete('set null'); // Nhân viên xử lý
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};
