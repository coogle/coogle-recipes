<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\Cusine;
use App\Models\Recipe\Ingredient;

class ImportRecipeMLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipe-ml:import {filespec* : The file(s) to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import one or more Recipe-ML formatted recipes into the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = $this->argument('filespec');
        
        $successes = 0;
        $failures = 0;
        
        $courseOther = Course::where('name', '=', 'Other')->first();
        $cusineOther = Cusine::where('name', '=', 'Other')->first();
        
        if(!$courseOther instanceof Course) {
            $this->error("We need an 'Other' Course to import into");
            return;
        }
        
        if(!$cusineOther instanceof Cusine) {
            $this->error("We need an 'Other' Cusine to import into");
            return;
        }
        
        foreach($files as $importFileName) {
            $this->info("Importing {$importFileName}");
            
            try {
                $sxe = simplexml_load_file($importFileName);
            } catch(\ErrorException $e) {
                $this->error("Failed to parse {$importFileName} : {$e->getMessage()}");
                $failures++;
                continue;
            }
            
            try {
                foreach($sxe->recipe as $recipeSxe) {
                    $title = (string)$recipeSxe->head->title;
                    
                    $ingredients = [];
                    $directions = [];
                    
                    foreach($recipeSxe->ingredients as $ingredient) {
                        if(isset($ingredient->{"ing-div"})) {
                            foreach($ingredient->{"ing-div"} as $divIngredient) {
                                foreach($divIngredient->ing as $divIngredientData) {
                                    $ingredients[] = [
                                        'ingredient' => (string)$divIngredientData->item,
                                        'quantity' => (string)$divIngredientData->amt->qty,
                                        'measurement' => $this->convertUnitString((string)$divIngredientData->amt->unit) 
                                    ];
                                }
                            }
                        } else {
                            foreach($ingredient->ing as $ingredientData) {
                                $ingredients[] = [
                                    'ingredient' => (string)$ingredientData->item,
                                    'quantity' => (string)$ingredientData->amt->qty,
                                    'measurement' => $this->convertUnitString((string)$ingredientData->amt->unit)
                                ];
                            }
                        }
                    }
                    
                    foreach($recipeSxe->directions as $direction) {
                        $directions[] = trim((string)$direction->step);
                    }
                    
                    $recipeObj = new Recipe();
                    $recipeObj->title = $title;
                    $recipeObj->course_id = $courseOther->id;
                    $recipeObj->favorite = false;
                    $recipeObj->cusine_id = $cusineOther->id;
                    $recipeObj->cook_mins = 0;
                    $recipeObj->prep_mins = 0;
                    $recipeObj->servings = 1;
                    $recipeObj->info = $title;
                    $recipeObj->directions = implode("\n", $directions);
                    
                    \DB::transaction(function() use ($recipeObj, $ingredients) {
                        
                        $recipeObj->save();
                        
                        foreach($ingredients as $ingredientData) {
                            $baseIngredientObj = \App\Models\Ingredient::findByString($ingredientData['ingredient']);
                            
                            if(!$baseIngredientObj instanceof \App\Models\Ingredient) {
                                $baseIngredientObj = new \App\Models\Ingredient();
                                $baseIngredientObj->name = $ingredientData['ingredient'];
                                $baseIngredientObj->save();
                            }
                            
                            $ingredient = new Ingredient();
                            $ingredient->recipe_id = $recipeObj->id;
                            $ingredient->quantity = $ingredientData['quantity'];
                            $ingredient->measurement = $ingredientData['measurement'];
                            $ingredient->ingredient_id = $baseIngredientObj->id;
                            $ingredient->save();
                        }
                    });
                    
                    $this->info("Imported '$title'");
                    
                    $successes++;
                }
            } catch(\Exception $e) {
                $this->error("Error processing {$importFileName} : {$e->getMessage()}");
                $failures++;
                continue;
            }
        }
        
        $this->info("Import complete.");
        $this->info("Successful Imports: {$successes}, Failed Imports: {$failures}");
    }
    
    protected function convertUnitString($string) {
        
        $string = strtolower(trim($string));
        
        if(empty($string)) {
            return 'piece';
        }
        
        switch($string) {
            case 'quarts':
            case 'quart':
            case 'qt':
            case 'qts':
                return 'quart';
            case 'gallon':
            case 'gallons':
                return 'gallon';
            case 'packages':
            case 'package':
            case 'cans':
            case 'can':
            case 'bunches':
            case 'large':
            case 'slice':
            case 'medium':
            case 'small':
            case 'container':
            case 'bunch':
            case 'slices':
            case 'clove':
            case 'cloves':
            case 'drop':
            case 'pieces':
            case 'piece':
            case 'drops':
                return 'piece';
            case 'lb':
            case 'pound':
            case 'pounds':
            case 'lbs':
                return 'lbs';
            case 'teaspoon':
            case 'teaspoons':
            case 'tsp':
            case 'tsp.':
            case 'tsps.':
            case 'tsps':
                return 'tsp';
            case 'tablespoon':
            case 'tablespoons':
            case 'tbsp':
            case 'tbsps':
            case 'tbsp.':
            case 'tbsps.':
                return 'tbsp';
            case 'cups':
            case 'cup':
                return 'cup';
            case 'ounces':
            case 'ounce':
            case 'oz':
            case 'ozs':
            case 'fluid ounces':
            case 'fluid ounce':
            case 'fluid oz':
            case 'fluid ozs':
                return 'oz';
            case 'pinch':
            case 'pinches':
            case 'dash':
            case 'dashes':
                return 'dash';
            default:
                throw new \Exception("Unknown Measurement: {$string}");
        }
    }
}
