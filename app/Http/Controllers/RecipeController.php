<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Recipe;
use App\Models\Course;
use App\Models\Cusine;
use App\Models\Recipe\Ingredient;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Course::all();
        $cusines = Cusine::all();
        
        return view('recipe.form', compact('courses', 'cusines'));
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
            'tags' => 'required|max:255',
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
                $validationRules["ingredients.$key.measurement"] = 'required|in:tsp,tbsp,cup,dash,piece,lbs';
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
        
        $recipe->save();
        
        foreach($ingredients as $ingredient) {
            $ingredientObj = new Ingredient();
            $ingredientObj->recipe_id = $recipe->id;
            $ingredientObj->quantity = $ingredient['quantity'];
            $ingredientObj->measurement = $ingredient['measurement'];
            $ingredientObj->preparation = $ingredient['preparation'];
            
            if(ctype_digit($ingredient['ingredient'])) {
                $baseIngredientObj = \App\Models\Ingredient::find($ingredient['ingredient']);
                
                if(!$baseIngredientObj instanceof \App\Models\Ingredient) {
                    continue;
                }
            } else {
                $baseIngredientObj = new \App\Models\Ingredient();
                $baseIngredientObj->name = $ingredient['ingredient'];
                $baseIngredientObj->save();
            }
            
            $ingredientObj->ingredient_id = $baseIngredientObj->id;
            $ingredientObj->save();
        }
        
        $recipe = Recipe::find($recipe->id);
        
        $request->session()->flash('flash.success', "Successfully added recipe!");
        
        return redirect()->route('home');
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
        
        return view('recipe.view', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        var_dump("edit");exit;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
