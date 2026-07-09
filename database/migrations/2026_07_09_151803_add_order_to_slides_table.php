<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderToSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slides_table', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('status');
        });

        // Backfill so existing slides keep their current (id-based) order
        // instead of all collapsing to the same "0".
        DB::table('slides_table')->orderBy('id')->select('id')->get()->each(function ($slide, $index) {
            DB::table('slides_table')->where('id', $slide->id)->update(['order' => $index]);
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
            $table->dropColumn('order');
        });
    }
}
