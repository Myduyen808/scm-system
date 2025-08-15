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
        Schema::table('products', function (Blueprint $table) {
            // đổi tên cột price thành regular_price
            $table->renameColumn('price', 'regular_price');

            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('regular_price', 'price');

            $table->dropColumn(['sale_price', 'image', 'sku', 'stock_quantity', 'supplier_id']);
        });
    }
};
