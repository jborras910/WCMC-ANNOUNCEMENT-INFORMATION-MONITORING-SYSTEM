<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryAndSubjectToActivityLogs extends Migration
{
    public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_logs', 'category')) {
                $table->string('category')->nullable()->after('activity');
            }
            if (!Schema::hasColumn('activity_logs', 'subject_email')) {
                $table->string('subject_email')->nullable()->after('category');
            }
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $cols = array_filter(['category', 'subject_email'], fn($c) => Schema::hasColumn('activity_logs', $c));
            if ($cols) $table->dropColumn(array_values($cols));
        });
    }
}
