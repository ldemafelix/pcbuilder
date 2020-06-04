<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->unsignedBigInteger('cpu_id')->nullable()->default(null);
            $table->unsignedBigInteger('gpu_id')->nullable()->default(null);
            $table->unsignedBigInteger('motherboard_id')->nullable()->default(null);
            $table->unsignedBigInteger('memory_id')->nullable()->default(null);
            $table->integer('memory_quantity')->nullable()->default(null);
            $table->unsignedBigInteger('casing_id')->nullable()->default(null);
            $table->unsignedBigInteger('power_supply_id')->nullable()->default(null);
            $table->unsignedBigInteger('cpu_cooler_id')->nullable()->default(null);
            $table->unsignedBigInteger('ssd_id')->nullable()->default(null);
            $table->unsignedBigInteger('hdd_id')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            // Set references
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('cpu_id')
                ->references('id')->on('parts');
            $table->foreign('gpu_id')
                ->references('id')->on('parts');
            $table->foreign('motherboard_id')
                ->references('id')->on('parts');
            $table->foreign('memory_id')
                ->references('id')->on('parts');
            $table->foreign('casing_id')
                ->references('id')->on('parts');
            $table->foreign('power_supply_id')
                ->references('id')->on('parts');
            $table->foreign('cpu_cooler_id')
                ->references('id')->on('parts');
            $table->foreign('ssd_id')
                ->references('id')->on('parts');
            $table->foreign('hdd_id')
                ->references('id')->on('parts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('builds');
    }
}
