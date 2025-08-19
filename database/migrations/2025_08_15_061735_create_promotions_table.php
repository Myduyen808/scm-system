<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên khuyến mãi
            $table->string('code')->nullable(); // Mã khuyến mãi
            $table->decimal('discount', 5, 2); // Giảm giá (% hoặc giá trị)
            $table->date('start_date')->nullable(); // Ngày bắt đầu (cũ)
            $table->date('end_date')->nullable(); // Ngày kết thúc (cũ)
            $table->date('valid_from')->nullable(); // Ngày bắt đầu (mới)
            $table->date('valid_to')->nullable(); // Ngày kết thúc (mới)
            $table->date('expiry_date')->nullable(); // Ngày hết hạn
            $table->text('description')->nullable(); // Mô tả
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->timestamps();
        });

        Schema::create('promotion_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unique(['promotion_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotion_product');
        Schema::dropIfExists('promotions');
    }
};
