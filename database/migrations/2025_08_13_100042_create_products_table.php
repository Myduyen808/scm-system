<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
