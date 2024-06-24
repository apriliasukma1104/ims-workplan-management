<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_project');
            $table->foreign('id_project')->references('id')->on('projects');
            $table->string('name_member');
            $table->string('type');
            $table->string('task');
            $table->string('description');
            $table->tinyInteger('start_month');
            $table->tinyInteger('end_month');
            $table->tinyInteger('tot_weight_plan');
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
        Schema::dropIfExists('task_plans');
    }
}
