<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightTaskPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weight_task_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project');
            $table->foreign('id_project')->references('id')->on('projects');
            $table->unsignedBigInteger('id_task_plan');
            $table->foreign('id_task_plan')->references('id')->on('task_plans');
            $table->string('month_plan');
            $table->float('weight_plan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weight_task_plans');
    }
}
