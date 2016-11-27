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
use GuzzleHttp\Client;
use App\Models\Recipe\Photo;

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
            'photo' => 'sometimes|image|dimensions:min_width=100,min_height=100'
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
            'directions' => 'required|min:5',
            'photo' => 'sometimes|image|dimensions:min_width=100,min_height=100'
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
        
        $photoObj = null;
        
        if($request->hasFile('photo') && $request->file('photo')->isValid()) {
            
            $photoObj = new Photo();
            $photoObj->photo_data = file_get_contents($request->file('photo')->getPathname());
        }
        
        $recipe->title = $request->input('title');
        $recipe->course_id = $request->input('course_id');
        $recipe->cusine_id = $request->input('cusine_id');
        $recipe->cook_mins = $request->input('cooktime');
        $recipe->prep_mins = $request->input('preptime');
        $recipe->servings = $request->input('servings');
        $recipe->info = $request->input('info');
        $recipe->directions = $request->input('directions');
        $recipe->tags = $request->input('tags');
        
        \DB::transaction(function() use ($recipe, $request, $photoObj) {
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
            
            if($photoObj instanceof Photo) {
                $photoObj->recipe_id = $recipe->id;
                $photoObj->save();
            }
            
   
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
    
    public function mirror(Request $request, $id)
    {
        $mirrorEndpoint = config('services.mirror.render_endpoint');
        
        $client = new Client([
            'timeout' => 10
        ]);
        
        
        $url = route('recipes.mirror-callback', compact('id'));
        
        try {
            $response = $client->post($mirrorEndpoint, [
                'form_params' => [
                    'callback' => $url
                ],
                'headers' => [
                    'X-Authorization' => config('services.mirror.key')
                ]
            ]);
            
            $responseResult = json_decode($response->getBody(), true);
            
            if(!$responseResult['success']) {
                $request->session()->flash('flash.error', 'Failed to transmit recipe to mirror');
                
                return \Redirect::route('recipes.show', compact('id'));
            }    
            
        } catch(\Exception $e) { 
            $request->session()->flash('flash.error', "There was an error communicating with the mirror");
            
            return \Redirect::route('recipes.show', compact('id'));
        }
        
        
        $request->session()->flash('flash.success', 'Transmitted recipe to mirror');
        
        return \Redirect::route('recipes.show', compact('id'));
                        
    }
    
    public function photo($recipeId, $dia)
    {
        // This can take awhile on the RPi
        
        set_time_limit(360);
        
        $parts = explode('x', $dia);
        $width = (int)trim($parts[0]);
        $height = (int)trim($parts[1]);
        
        if(($width < 10) && ($height < 10)) { 
            throw new \Exception("Invalid Request");
        }
        
        $recipe = Recipe::findOrFail($recipeId);
        
        $photo = $recipe->getPhotoByResolution($width, $height);
        
        if(!$photo instanceof Photo) {
            throw new \Exception("Could not load photo for resolution");
        }
        
        return response($photo->photo)->header('Content-Type', $photo->mimetype);
    }
    
}
