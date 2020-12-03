<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateImportMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('module_id');

            // Compatibility with Laravel < 5.8
            if (DB::getSchemaBuilder()->getColumnType('users', 'id') === 'bigint') { // Laravel >= 5.8
                $table->unsignedBigInteger('user_id')->nullable();
            } else { // Laravel < 5.8
                $table->unsignedInteger('user_id')->nullable();
            }

            $table->string('name');
            $table->text('config');
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('uccello_modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_mappings');
    }
}
