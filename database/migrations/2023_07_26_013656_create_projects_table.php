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
            $table->string('description');
            $table->enum('project_type', ['Program Kerja', 'Kegiatan Rutin', 'Investasi']);
            $table->unsignedInteger('team_leader');
            $table->json('team_members')->nullable();
            $table->year('year');
            $table->decimal('budget');
            $table->string('status')->default('Created');
            $table->timestamps();

            // Menambahkan foreign key constraint untuk team_leader
            $table->foreign('team_leader')->references('id')->on('members');
        });

        Schema::create('projects_has_members', function (Blueprint $table) {
            $table->unsignedInteger('id_projects');
            $table->unsignedInteger('id_members');

            $table->foreign('id_projects')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('id_members')->references('id')->on('members')->onDelete('cascade');

            $table->primary(['id_projects', 'id_members']);
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
        Schema::dropIfExists('projects_has_members');
    }
}
