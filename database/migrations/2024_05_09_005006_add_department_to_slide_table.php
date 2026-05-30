<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentToSlideTable extends Migration
{
    public function up()
    {
        Schema::table('slides_table', function (Blueprint $table) {
            if (!Schema::hasColumn('slides_table', 'added_by_email')) $table->string('added_by_email')->nullable()->after('status');
            if (!Schema::hasColumn('slides_table', 'department'))      $table->string('department')->nullable()->after('added_by_email');
        });
    }

    public function down()
    {
        Schema::table('slides_table', function (Blueprint $table) {
            $cols = array_filter(['added_by_email', 'department'], fn($c) => Schema::hasColumn('slides_table', $c));
            if ($cols) $table->dropColumn(array_values($cols));
        });
    }
}
