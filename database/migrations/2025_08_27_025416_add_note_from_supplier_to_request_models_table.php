<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteFromSupplierToRequestModelsTable extends Migration
{
    public function up()
    {
        Schema::table('request_models', function (Blueprint $table) {
            $table->text('note_from_supplier')->nullable()->after('employee_note');
        });
    }

    public function down()
    {
        Schema::table('request_models', function (Blueprint $table) {
            $table->dropColumn('note_from_supplier');
        });
    }
}
