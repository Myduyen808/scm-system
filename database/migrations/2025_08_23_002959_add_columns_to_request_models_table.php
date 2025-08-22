<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRequestModelsTable extends Migration
{
    public function up()
    {
        Schema::table('request_models', function (Blueprint $table) {
            if (!Schema::hasColumn('request_models', 'note')) {
                $table->string('note')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('request_models', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
}
