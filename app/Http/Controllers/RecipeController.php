<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\Cusine;
use App\Models\Recipe\Ingredient;
use App\Recipe\Exporter;
use Carbon\Carbon;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipes = Recipe::orderBy('title')->paginate(15);
        
        return view('recipe.list', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $courses = Course::pluck('name', 'id');
        $cusines = Cusine::pluck('name', 'id');
        
        return view('recipe.create', compact('courses', 'cusines', 'request'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'title' => 'required|unique:recipes|max:255',
            'tags' => 'sometimes|max:255',
            'course_id' => 'required|exists:courses,id',
            'cusine_id' => 'required|exists:cusines,id',
            'cooktime' => 'sometimes|integer',
            'preptime' => 'sometimes|integer',
            'servings' => 'required|integer',
            'ingredients' => 'required|array',
            'directions' => 'required|min:5',
            'photo' => 'sometimes|image|dimensions:min_width=100,min_height=100,max_width=200,max_height=200,ratio=1'
        ];
        
        $ingredients = $request->input('ingredients');
        
        if(is_array($ingredients)) {
            foreach($ingredients as $key => $val) {
                $validationRules["ingredients.$key.quantity"] = 'required|fractionVal';
                $validationRules["ingredients.$key.measurement"] = 'required|in:tsp,tbsp,oz,cup,dash,piece,lbs,quart,gallon';
                $validationRules["ingredients.$key.preparation"] = "sometimes|max:255";
                $validationRules["ingredients.$key.ingredient"] = "required|max:255";
            }
        }
        
        $this->validate($request, $validationRules);
        
        $recipe = new Recipe();
        
        $recipe->title = $request->input('title');
        $recipe->course_id = $request->input('course_id');
        $recipe->favorite = false;
        $recipe->photo_url = '';
        $recipe->cusine_id = $request->input('cusine_id');
        $recipe->cook_mins = $request->input('cooktime');
        $recipe->prep_mins = $request->input('preptime');
        $recipe->servings = $request->input('servings');
        $recipe->info = $request->input('info');
        $recipe->directions = $request->input('directions');
        $recipe->tags = $request->input('tags');
        
        if(empty($recipe->cook_mins)) {
            $recipe->cook_mins = 0;
        }
        
        if(empty($recipe->prep_mins)) {
            $recipe->prep_mins = 0;
        }
        
        \DB::transaction(function() use ($recipe, $ingredients) {
            $recipe->save();
            
            foreach($ingredients as $ingredient) {
                $ingredientObj = new Ingredient();
                $ingredientObj->recipe_id = $recipe->id;
                $ingredientObj->quantity = $ingredient['quantity'];
                $ingredientObj->measurement = $ingredient['measurement'];
                $ingredientObj->preparation = $ingredient['preparation'];
            
                $baseIngredientObj = \App\Models\Ingredient::findByString($ingredient['ingredient']);
            
                if(!$baseIngredientObj instanceof \App\Models\Ingredient) {
                    $baseIngredientObj = new \App\Models\Ingredient();
                    $baseIngredientObj->name = $ingredient['ingredient'];
                    $baseIngredientObj->save();
                }
            
                $ingredientObj->ingredient_id = $baseIngredientObj->id;
                $ingredientObj->save();
            }
        });
        
        $recipe = Recipe::find($recipe->id);
        
        $request->session()->flash('flash.success', "Successfully added recipe!");
        
        return redirect()->route('recipes.show', ['id' => $recipe->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->increment('views');
        return view('recipe.view', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $courses = Course::pluck('name', 'id');
        $cusines = Cusine::pluck('name', 'id');
        $recipe = Recipe::findOrFail($id);
        
        return view('recipe.edit', compact('courses', 'cusines', 'recipe', 'request'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        
        $validationRules = [
            'title' => 'required|max:255',
            'tags' => 'sometimes|max:255',
            'course_id' => 'required|exists:courses,id',
            'cusine_id' => 'required|exists:cusines,id',
            'cooktime' => 'sometimes|integer',
            'preptime' => 'sometimes|integer',
            'servings' => 'required|integer',
            'ingredients' => 'required|array',
            'directions' => 'required|min:5'
        ];
        
        $ingredients = $request->input('ingredients');
        
        if(is_array($ingredients)) {
            foreach($ingredients as $key => $val) {
                $validationRules["ingredients.$key.quantity"] = 'required|fractionVal';
                $validationRules["ingredients.$key.measurement"] = 'required|in:tsp,tbsp,cup,dash,piece,oz,lbs,quart,gallon';
                $validationRules["ingredients.$key.preparation"] = "sometimes|max:255";
                $validationRules["ingredients.$key.ingredient"] = "required|max:255";
            }
        }
        
        $this->validate($request, $validationRules);
        
        $recipe->title = $request->input('title');
        $recipe->photo_url = null;
        $recipe->course_id = $request->input('course_id');
        $recipe->favorite = false;
        $recipe->cusine_id = $request->input('cusine_id');
        $recipe->cook_mins = $request->input('cooktime');
        $recipe->prep_mins = $request->input('preptime');
        $recipe->servings = $request->input('servings');
        $recipe->info = $request->input('info');
        $recipe->directions = $request->input('directions');
        $recipe->tags = $request->input('tags');
        
        \DB::transaction(function() use ($recipe, $request) {
            $recipe->ingredients()->delete();
            
            foreach($request->input('ingredients') as $ingredient) {
                $ingredientObj = new Ingredient();
                $ingredientObj->recipe_id = $recipe->id;
                $ingredientObj->quantity = $ingredient['quantity'];
                $ingredientObj->measurement = $ingredient['measurement'];
                $ingredientObj->preparation = $ingredient['preparation'];
            
                $baseIngredientObj = \App\Models\Ingredient::findByString($ingredient['ingredient']);
            
            
                if(!$baseIngredientObj instanceof \App\Models\Ingredient) {
                    $baseIngredientObj = new \App\Models\Ingredient();
                    $baseIngredientObj->name = $ingredient['ingredient'];
                    $baseIngredientObj->save();
                }
            
                $ingredientObj->ingredient_id = $baseIngredientObj->id;
                $ingredientObj->save();
            }
            
            $recipe->save();            
        });
        
        $request->session()->flash('flash.success', "Successfully saved recipe!");
        
        return redirect()->route('recipes.show', ['id' => $recipe->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        
        $request->session()->flash('flash.success', "Recipe Successfully Deleted");
        return redirect()->route('home');
    }
    
    public function export()
    {
        $exportFileName = 'cooglerecipe-export-' . Carbon::now()->format('m-d-Y') . '-' . uniqid() . '.xml';
        
        $headers = [
            'Content-Type' => 'text/xml',
            'Cache-Control' => 'public',
            'Content-Description' => 'CoogleRecipe Recipe Export',
            'Content-Disposition' => "attachment; filename=$exportFileName",
            'Content-Transfer-Encoding' => 'binary'
        ];
        
        return response()->stream(function() {
            Recipe::exportAll('php://output');
        }, 200, $headers);
        
    }
    
}
