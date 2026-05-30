<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role'))       $table->string('role')->nullable()->after('middle_name');
            if (!Schema::hasColumn('users', 'department')) $table->string('department')->nullable()->after('role');
            if (!Schema::hasColumn('users', 'username'))   $table->string('username')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = array_filter(['role', 'department', 'username'], fn($c) => Schema::hasColumn('users', $c));
            if ($cols) $table->dropColumn(array_values($cols));
        });
    }
}
