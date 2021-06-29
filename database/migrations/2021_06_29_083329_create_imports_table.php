<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(env('UCCELLO_TABLE_PREFIX', 'uccello_').'imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id');
            $table->unsignedInteger('module_id');
            $table->foreignId('user_id')->constrained();
            $table->json('config');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->foreign('domain_id')
                ->references('id')
                ->on(env('UCCELLO_TABLE_PREFIX', 'uccello_').'domains');

            $table->foreign('module_id')
                ->references('id')
                ->on(env('UCCELLO_TABLE_PREFIX', 'uccello_').'modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(env('UCCELLO_TABLE_PREFIX', 'uccello_').'imports');
    }
}
