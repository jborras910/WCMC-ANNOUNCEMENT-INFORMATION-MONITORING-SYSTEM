<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentToSlideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slides_table', function (Blueprint $table) {
            $table->string('added_by_email')->nullable()->after('status');
            $table->string('department')->nullable()->after('added_by_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slides_table', function (Blueprint $table) {
            $table->dropColumn(['added_by_email', 'department']);
        });
    }
}
