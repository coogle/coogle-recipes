# CoogleRecipes

A Laravel 5.3 / Bootstrap Recipe Management System I wrote for my own personal use. It runs on a 
Raspberry Pi system as part of my smart home and ties into other things like my Magic Mirror project
(see coogle/cooglemirror).

I wrote this instead of using things I could find online because I couldn't find anything that met
my criteria:

- Modern look and feel w/Mobile support (i.e. Bootstrap based)
- The ability to access the recipes in the database via API call
- Built in the last 10 years
- Written in PHP w/ best practices

I am a Laravel guy, so I wrote this in Laravel 5.3. I haven't decided on a backend yet (currently it's MySQL) but should work fine with SQLite as well as everything is Eloquent ORM based.

It's not overloaded with features, but I'm sure I'll add them as needed. Also not built to scale beyond something you'd run on a Raspberry Pi. If you want CDN support, etc. feel free to submit a PR.

The project itself was started based on my Skeleton repository (coogle/skeleton on the php7-laravel53 branch). This means you can immediately get the entire thing up and running using vagrant and VirtualBox by copying VagrantConfig.example.json to VagrantConfig.json, editing, and running vagrant up. See the README in my skeleton repo for more instructions.

Features thus far, or at least last time I updated this README:

- Create/Edit Recipes, with an image
- Markdown used for directions / recipe info
- Tags and some basic categorizations
- List/Search recipes by title or tag
- Export Recipes in [RecipeML](http://www.formatdata.com/recipeml/) format
- An API to fetch recipes 

Things I'm getting around to:

- TypeAhead integration for ingredients to keep the ingredient list normalized, etc.
- More advanced searching (filter by category + query, etc.)
- Actually building the API to access recipes
- Integrating coogle/recipe-parser so we can auto-populate new recipes by the URL we found them.

Fork and go forth, maybe submit a PR if you do something cool with it.
