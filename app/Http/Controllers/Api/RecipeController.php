<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Recipe;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipes = \App\Models\Recipe::paginate(50);
        
        return response()->json($recipes);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = \App\Models\Recipe::findOrFail($id);
        
        $recipe->views++;
        $recipe->save();
        
        return response()->json($recipe);
    }
    
    public function search(Request $request)
    {
        $key = $request->input('q');
        $favOnly = (bool)$request->input('f', false);
        
        $recipes = \App\Models\Recipe::where('title', 'LIKE', "%$key%")
                                    ->orWhere('tags', 'LIKE', "%$key%");
        
        if($favOnly) {
            $recipes->where('favorite', '=', true);
        }
        
        $recipes = $recipes->get();
        
        return response()->json($recipes);
    }
    
    public function favorite($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->favorite = true;
        $recipe->save();
    
        return response()->json(['success' => true]);
    }
    
    public function unfavorite($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->favorite = false;
        $recipe->save();
    
        return response()->json(['success' => true]);
    }
    
    public function mirrorCallback($id)
    {
        $sections = [
            'top_bar',
            'top_left',
            'top_center',
            'top_right',
            'right_middle',
            'left_middle',
            'upper_third',
            'middle_center',
            'lower_third',
            'bottom_bar',
            'bottom_left',
            'bottom_center',
            'bottom_right'
        ];
        
        $retval = [];
        
        $recipe = Recipe::find($id);
        
        if(!$recipe instanceof Recipe) {
            
            return response()->json([
                'middle_center' => "There was an error locating the requested recipe."
            ]);
            
        } else {
        
            foreach($sections as $section) {
                try {
                    $retval[$section] = (string)\View::make("recipe.mirror.$section", compact('recipe'))->render();
                } catch(\Exception $e) {
                    $retval[$section] ='';
                }
            }
        }
        
        return response()->json($retval);
    }
}
