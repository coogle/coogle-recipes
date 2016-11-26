<table style="width: 400px">
    <tr>
        <td colspan="3" class="normal bright" align="center">{{ $recipe->title }}</td>
    </tr>
    <tr>
        <td colspan="3">
            @if($recipe->hasPhoto())
            <img src="{{ route('recipes.photo', ['recipeId' => $recipe->id, 'dia' => '400x284']) }}" style="width:400px; height:284;">
            @else
            <img src="http://placehold.it/400x284" style="width:400px; height:284;">
            @endif
        </td>
    </tr>
    <tr>
        <td><span class="fa fa-fw fa-clock-o"></span></td>
        <td>Prep Time</td>
        <td class="large bright">{{ $recipe->prep_mins or '??'}}&nbsp;min</td>
    </tr>
    <tr>
        <td><span class="fa fa-fw fa-clock-o"></span></td>
        <td>Cook Time</td>
        <td class="large bright">{{ $recipe->cook_mins or '??'}}&nbsp;min</td>
    </tr>
</table>
