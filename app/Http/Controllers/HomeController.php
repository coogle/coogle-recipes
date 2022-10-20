<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $popularRecipes = Recipe::orderBy('views', 'desc')
                                ->orderBy('favorite', 'desc')
                                ->get();
        
        return view('home', compact('popularRecipes'));
    }
}
