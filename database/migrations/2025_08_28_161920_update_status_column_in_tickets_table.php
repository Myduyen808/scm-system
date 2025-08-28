<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'pending', 'assigned', 'replied', 'closed'])
                  ->default('open')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'closed'])
                  ->default('open')
                  ->change();
        });
    }
};
