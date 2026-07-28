<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// The old free-text `department` columns are now fully superseded by
// `department_id` (backfilled by DepartmentsSeeder). They can't be left in
// place: a column named `department` shadows the Eloquent `department()`
// relation of the same name — `$model->department` silently returns the
// stale string instead of the related Department model.
class DropLegacyDepartmentStringColumns extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
        });

        Schema::table('slides_table', function (Blueprint $table) {
            if (Schema::hasColumn('slides_table', 'department')) {
                $table->dropColumn('department');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('department_id');
            }
        });

        Schema::table('slides_table', function (Blueprint $table) {
            if (!Schema::hasColumn('slides_table', 'department')) {
                $table->string('department')->nullable()->after('department_id');
            }
        });
    }
}
