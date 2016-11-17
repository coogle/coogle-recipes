@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Popular Recipes</div>

                <div class="panel-body">
                    <div class="col-md-12">
                        
                        @foreach($popularRecipes as $recipe)
                        <div class="media">
                            <a class="media-left waves-light">
                                <img class="rounded-circle" src="http://placehold.it/100x100" alt="{{ $recipe->title }}">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading"><a href="{{ route('recipes.show', ['id' => $recipe->id]) }}">{{ $recipe->title }}</a></h4>
                                <p>{{ $recipe->info }}</p>
                                <p class="pull-right"><a href="{{ route('recipes.show', ['id' => $recipe->id]) }}" class="btn btn-sm btn-primary">view recipe <span class="glyphicon glyphicon-chevron-right"></span></a></p>
                            </div>
                        </div>
                        @endforeach
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
