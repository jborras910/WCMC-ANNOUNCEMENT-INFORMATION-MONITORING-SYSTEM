<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentIdToUsersAndSlides extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('department');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            }
        });

        Schema::table('slides_table', function (Blueprint $table) {
            if (!Schema::hasColumn('slides_table', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('department');
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });

        Schema::table('slides_table', function (Blueprint $table) {
            if (Schema::hasColumn('slides_table', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }
}
