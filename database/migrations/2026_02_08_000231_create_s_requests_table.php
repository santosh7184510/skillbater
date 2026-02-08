<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_requests', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('from_user'); // who wants the skill
    $table->unsignedBigInteger('to_user');   // owner of the skill
    $table->unsignedBigInteger('skill_id');  // skill being requested
    $table->enum('status', ['pending','accepted','rejected'])->default('pending');
    $table->timestamps();

    $table->foreign('from_user')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('to_user')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_requests');
    }
}
