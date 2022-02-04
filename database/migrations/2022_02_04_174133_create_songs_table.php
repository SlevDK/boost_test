<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name');
            $table->ipAddress('ip');
            // Duration in sec. Unsigned medium (24 bit) int = 16777215, ~4660h
            $table->unsignedMediumInteger('duration')->default(0);
            // Total duration in sec. Unsigned (32 bit) int = 4294967295, ~1193046h
            $table->unsignedInteger('total_duration');

            $table->timestamps();

            $table->index('total_duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
