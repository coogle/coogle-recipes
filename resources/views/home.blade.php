@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Popular Recipes</div>

                <div class="panel-body">
                    <div class="col-md-12">
                        
                        @forelse($popularRecipes as $recipe)
                        <div class="media">
                            <a class="media-left waves-light">
                                <img class="rounded-circle" src="http://placehold.it/120x120" width="120" height="120" alt="{{ $recipe->title }}">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading">
                                    <a href="{{ route('recipes.show', ['id' => $recipe->id]) }}">{{ $recipe->title }}</a>
                                    @if($recipe->favorite)
                                    <span class="glyphicon glyphicon-star"></span>
                                    @endif 
                                </h4>
                                <p>{{ strip_tags(Markdown::convertToHtml($recipe->info)) }}</p>
                                <p class="pull-right"><a href="{{ route('recipes.show', ['id' => $recipe->id]) }}" class="btn btn-sm btn-primary">view recipe <span class="glyphicon glyphicon-chevron-right"></span></a></p>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12 text-center">
                            No Recipes Yet, better get going!
                            
                            <div class="col-md-6 col-md-offset-3">
                                <a href="{{ route('recipes.create') }}" class="btn btn-primary btn-block">Create Recipe</a>
                            </div>
                        </div>
                        @endforelse
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
