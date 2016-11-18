@extends('layouts.app')

@section('stylesheets')
@parent
<link rel="stylesheet" href="/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css"/>
<link rel="stylesheet" href="/bower_components/bootstrap-markdown/css/bootstrap-markdown.min.css"/>
<style>
.container{
    margin-top:20px;
}
.image-preview-input {
    position: relative;
	overflow: hidden;
	margin: 0px;    
    color: #333;
    background-color: #fff;
    border-color: #ccc;    
}
.image-preview-input input[type=file] {
	position: absolute;
	top: 0;
	right: 0;
	margin: 0;
	padding: 0;
	font-size: 20px;
	cursor: pointer;
	opacity: 0;
	filter: alpha(opacity=0);
}
.image-preview-input-title {
    margin-left:2px;
}
</style>
@stop

@section('javascript')
@parent
<script src="/bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script src="/bower_components/bootstrap-markdown/js/bootstrap-markdown.js"></script>
<script>

$(document).on('click', '#close-preview', function(){ 
    $('.image-preview').popover('hide');
    // Hover befor close the preview
    $('.image-preview').hover(
        function () {
           $('.image-preview').popover('show');
        }, 
         function () {
           $('.image-preview').popover('hide');
        }
    );    
});

$('#addIngredientBtn').on('click', function(e) {
	e.preventDefault();
	
	var template = $('#ingredientLineTemplate').clone();

	template.attr('id', '')
			.addClass('ingredientItem')
			.show();

	$('#ingredientContainer').append(template);
});

$('#recipeForm').on('submit', function(e) {
	$('#ingredientContainer').children().each(function(idx) {
		$(this).find('.quantityInput').attr('name', 'ingredients[' + $(this).index() + '][quantity]');
		$(this).find('.measurementInput').attr('name', 'ingredients[' + $(this).index() + '][measurement]');
		$(this).find('.ingredientInput').attr('name', 'ingredients[' + $(this).index() + '][ingredient]');
	    $(this).find('.preparationInput').attr('name', 'ingredients[' + $(this).index() + '][preparation]');
	});
});

$(document).on('click', '.deleteIngredient', function(e) {
	e.preventDefault();
	$(e.target).parents('.ingredientItem').remove();
});

$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
        $('.image-preview').attr("data-content","").popover('hide');
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview-input input:file').val("");
        $(".image-preview-input-title").text("Browse"); 
    }); 
    // Create the preview image
    $(".image-preview-input input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200
        });      
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("Change");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);            
            img.attr('src', e.target.result);
            $(".image-preview").attr("data-content",$(img)[0].outerHTML).popover("show");
        }        
        reader.readAsDataURL(file);
    });  
});
</script>
@stop

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">Create Recipe</div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'recipes.store','files' => true]) !!}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="title">Recipe Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Enter Recipe Title" id="title">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="photo">Recipe Photo</label>
                                <div class="input-group image-preview">
                                    <input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                                    <span class="input-group-btn">
                                        <!-- image-preview-clear button -->
                                        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                            <span class="glyphicon glyphicon-remove"></span> Clear
                                        </button>
                                        <!-- image-preview-input -->
                                        <div class="btn btn-default image-preview-input">
                                            <span class="glyphicon glyphicon-folder-open"></span>
                                            <span class="image-preview-input-title">Browse</span>
                                            <input type="file" accept="image/png, image/jpeg, image/gif" name="photo"/> <!-- rename it -->
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tags">Tags</label>
                            <input type="text" name="tags" id="tags" data-role="tagsinput" class="form-control"/>
                        </div>
                        <div class="form-group row">
                            <label for="course" class="col-xs-1 col-form-label">Course</label>
                            <div class="col-xs-5">
                                <select id="course" name="course_id" class="form-control">
                                    @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="cusine" class="col-xs-1 col-form-label">Cusine</label>
                            <div class="col-xs-5">
                                <select id="cusine" name="cusine_id" class="form-control">
                                    @foreach($cusines as $cusine)
                                    <option value="{{ $cusine->id }}">{{ $cusine->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cooktime" class="col-xs-1 col-form-label">Cook Time</label>
                            <div class="col-xs-5">
                                <input type="text" name="cooktime" id="cooktime" class="form-control"/>
                            </div>
                            <label for="preptime" class="col-xs-1 col-form-label">Prep Time</label>
                            <div class="col-xs-5">
                                <input type="text" name="preptime" id="preptime" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="servings" class="col-xs-1 col-form-label">Servings</label>
                            <div class="col-xs-5">
                                <input type="text" name="servings" id="servings" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="info">Recipe Info</label>
                            <textarea data-provide="markdown" rows="5" class="form-control" id="info" name="info"></textarea>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title pull-left">Ingredients</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row" style="padding-right: 10px">
                                    <button class="pull-right btn btn-sm btn-primary" id="addIngredientBtn">Add Ingredient</button>
                                </div>
                                <hr width="50%"/>
                                <div class="row">
                                    <div class="col-xs-2 col-xs-offset-1">Quantity</div>
                                    <div class="col-xs-3">Measurement</div>
                                    <div class="col-xs-3">Preparation</div>
                                    <div class="col-xs-2">Ingredient</div>
                                </div>
                                <div id="ingredientContainer"></div>
                                <div class="row" id="ingredientLineTemplate" style="display:none">
                                    <div class="form-group row">
                                        <div class="col-xs-2 col-xs-offset-1">
                                            <input type="text" class="form-control quantityInput">
                                        </div>
                                        <div class="col-xs-3">
                                            <select class="form-control measurementInput">
                                                <option value="tsp">tsp</option>
                                                <option value="tbsp">tbsp</option>
                                                <option value="cup">cup</option>
                                                <option value="dash">dash</option>
                                                <option value="lbs">lbs</option>
                                                <option value="piece">piece</option>
                                            </select>
                                        </div>
                                        <div class="col-xs-3">
                                            <input type="text" class="form-control preparationInput"/>
                                        </div>
                                        <div class="col-xs-2">
                                            <input type="text" class="form-control ingredientInput"/>
                                        </div>
                                    </div>
                                        <div class="col-xs-12"><sup class="pull-right"><a class="deleteIngredient" href="#"><span class="glyphicon glyphicon-trash"></span> delete</a></sup></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="directions">Recipe Directions</label>
                            <textarea data-provide="markdown" rows="5" class="form-control" id="directions" name="directions"></textarea>
                        </div>
                        <div class="col-md-4 col-md-offset-4">
                            <button type="submit" class="btn btn-primary btn-block">Save Recipe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop