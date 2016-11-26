<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_photos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id')->unsigned();
            $table->string('mimetype');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();    
        });
        
        Schema::table('recipes', function(Blueprint $table) {
            $table->dropColumn('photo_url'); 
        });
        
        DB::statement("ALTER TABLE recipe_photos ADD photo MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipe_photos');
        
        Schema::table('recipes', function(Blueprint $table) {
            $table->string('photo_url')->nullable();
        });
    }
}
