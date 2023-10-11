<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('project_type', ['RKAP', 'KPI', 'NPM']);
            $table->unsignedBigInteger('team_leader');
            $table->json('team_members')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'to do', 'doing', 'submission', 'done']);
            $table->string('description');
            $table->enum('validation', ['approved', 'not approved']);
            $table->string('note');
            $table->timestamps();

            // Menambahkan foreign key constraint untuk team_leader
            $table->foreign('team_leader')->references('id')->on('members');
        });

        Schema::create('project_team_members', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('member_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');

            $table->primary(['project_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_team_members');
    }
}
