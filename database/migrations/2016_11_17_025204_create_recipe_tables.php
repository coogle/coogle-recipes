<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function(Blueprint $table) {
           $table->increments('id');
           $table->string('name')->unique();
           $table->timestamps();
        });
        
        Schema::create('cusines', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('ingredients', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });
        
        Schema::create('recipes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title')->index();
            $table->string('photo_url')->nullable();
            $table->integer('course_id')->unsigned();
            $table->boolean('favorite')->default(false);
            $table->integer("cusine_id")->unsigned();
            $table->integer('cook_mins')->nullable();
            $table->integer('prep_mins')->nullable();
            $table->integer('servings')->nullable();
            $table->mediumText('info')->nullable();
            $table->mediumText('directions');
            $table->text('tags')->nullable();
            $table->timestamps();
            
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses');
                  
            $table->foreign('cusine_id')
                  ->references('id')
                  ->on('cusines');
        });
        
        Schema::create('recipe_ingredients', function(Blueprint $table) {
           
            $table->increments('id');
            $table->integer('recipe_id')->unsigned();
            $table->string('quantity');
            $table->string('measurement');
            $table->integer('ingredient_id')->unsigned();
            $table->string('preparation')->nullable();
            $table->timestamps();
            
            $table->foreign('recipe_id')
                  ->references('id')
                  ->on('recipes')
                  ->onDelete('cascade');
            
            $table->foreign('ingredient_id')
                  ->references('id')
                  ->on('ingredients');
                  
        });

        DB::statement('CREATE INDEX tags_idx ON recipes (tags(1024));');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipes');
        Schema::drop('courses');
        Schema::drop('cusines');
        Schema::drop("recipe_ingredients");
        Schema::drop("ingredients");
    }
}
