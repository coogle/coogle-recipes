@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Recipes</div>

                <div class="panel-body">
                    <div class="col-md-12">
                        @forelse($recipes as $recipe)
                        <div class="media">
                            <a class="media-left waves-light">
                                @if($recipe->hasPhoto())
                                <img style="border: thin solid black;" class="rounded-circle" src="{{ route('recipes.photo', ['recipeId' => $recipe->id, 'dia' => '120x120']) }}" width="120" height="120" alt="{{ $recipe->title }}">
                                @else
                                <img style="border: thin solid black;" class="rounded-circle" src="https://via.placeholder.com/120x120" width="120" height="120" alt="{{ $recipe->title }}">
                                @endif
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
                            No Results
                        </div>
                        @endforelse
                    </div>
                    @if(isset($q))
                        {{ $recipes->appends(['q' => $q])->links() }}
                    @else
                        {{ $recipes->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
