<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip');
            $table->enum('position',['Kepala Departemen','Kepala Bagian','Staf','IT Help Desk']);
            $table->enum('sub_department',['Infrastructure & Support', 'ERP & EIM', 'Apps Development & Governance']);
            $table->enum('role',['Kadep','Kabag','Staf','Admin']);
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('members_status')->default(0); // 0: Inactive, 1: Active
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
        Schema::dropIfExists('members');
    }
}
