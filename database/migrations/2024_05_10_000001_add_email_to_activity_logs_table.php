<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailToActivityLogsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('activity_logs', 'email')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('email')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('activity_logs', 'email')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
}
