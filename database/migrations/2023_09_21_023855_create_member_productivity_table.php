<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberProductivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_productivity', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->id();
            $table->foreign('id_project')->references('id')->on('projects');
            $table->foreign('id_task')->references('id')->on('tasks');
            $table->foreign('id_member')->references('id')->on('members');
            $table->string('comment');
            $table->string('subject');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
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
        Schema::dropIfExists('member_productivity');
    }
}
