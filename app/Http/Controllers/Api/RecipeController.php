<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        
        $recipes = \App\Models\Recipe::where('title', 'LIKE', "%$key%")
                                    ->orWhere('tags', 'LIKE', "%$key%")
                                    ->get();
        
        return response()->json($recipes);
    }

}
