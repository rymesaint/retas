<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BranchsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('branchName');
            $table->string('location')->nullable();
            $table->unsignedBigInteger('manager')->nullable();
            $table->integer('percentagePrice')->nullable()->default(0);
            $table->integer('status');
            $table->text('annotation')->nullable();
            $table->boolean('isMainBranch')->nullable()->default(false);
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
        Schema::dropIfExists('branches');
    }
}
