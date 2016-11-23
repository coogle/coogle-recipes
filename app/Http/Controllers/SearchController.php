<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        
        $recipes = Recipe::where('title', 'LIKE', "%$q%")
                         ->orWhere('tags', 'LIKE',"%$q%")
                         ->paginate(15);
        
        return view('recipe.list', compact('recipes', 'q'));
        
    }
}
