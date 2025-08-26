<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeNoteToRequestModelsTable extends Migration
{
    public function up()
    {
        Schema::table('request_models', function (Blueprint $table) {
            $table->text('employee_note')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('request_models', function (Blueprint $table) {
            $table->dropColumn('employee_note');
        });
    }
}
