@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Recipe Details</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-7">
                            <h2 style="margin-top:0px">{{ $recipe->title }}</h2>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <img src="http://placehold.it/200x200">
                                    </div>
                                    <h4>About Recipe</h4>
                                    <p>{{ $recipe->info }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="list-group">
                                <li class="list-group-item text-center list-group-item-info">
                                    <strong><span class="glyphicon glyphicon-time"></span> Time to Meal</strong>
                                </li>
                                <li class="list-group-item">
                                    <span class="badge">{{ $recipe->prep_mins }} mins</span>
                                    Prep Time
                                </li>
                                <li class="list-group-item">
                                    <span class="badge">{{ $recipe->cook_mins }} mins</span>
                                    Cook Time
                                </li>
                                <li class="list-group-item text-center list-group-item-info">
                                    <strong><span class="glyphicon glyphicon-asterisk"></span> Actions</strong>
                                </li>
                                <a class="list-group-item" href="{{ route('recipes.edit', ['id' => $recipe->id]) }}"><span class="glyphicon glyphicon-edit"></span> Edit Recipe</a>
                                <a class="list-group-item" href="{{ route('recipes.destroy', ['id' => $recipe->id]) }}"><span class="glyphicon glyphicon-trash"></span> Delete Recipe</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <hr>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-group col-md-5">
                                <li class="list-group-item text-center list-group-item-info">
                                    <strong><span class="glyphicon glyphicon-list"></span> Ingredients</strong>
                                </li>
                                @foreach($recipe->ingredients as $ingredient)
                                <li class="list-group-item">
                                    <span class="badge">{{ $ingredient->quantity }} {{ $ingredient->measurement }}</span>
                                    {{ $ingredient->ingredient->name }} @if($ingredient->preparation)({{ $ingredient->preparation }})@endif
                                </li>
                                @endforeach
                            </div>
                            <h2 style="margin-top:0px">Instructions</h2>
                            <p>{{ $recipe->directions }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

