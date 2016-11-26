<table style="width:550px">
    <tr>
        <td align="center" class="bright" style="border-bottom: thin solid white;">Ingredients<br/><span class="small">({{ $recipe->servings }} servings)</span></td>
    </tr>
    @foreach($recipe->ingredients as $ingredient)
        <tr>
            <td class="small">{{ $ingredient->preparation}}</td>
            <td class="small">{{ $ingredient->name }}</td>
            <td class="small">({{ $ingredient->quantity }} {{ $ingredient->measurement }})</td> 
        </tr>
    @endforeach
</table>