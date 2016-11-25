<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\MySqlConnection;

class MigrateToInnodb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!DB::connection() instanceof MySqlConnection) {
            return;
        }
        
        \DB::statement('ALTER TABLE users ENGINE=InnoDb');
        \DB::statement('ALTER TABLE password_resets ENGINE=InnoDb');
        \DB::statement('ALTER TABLE recipes ENGINE=InnoDb');
        \DB::statement('ALTER TABLE courses ENGINE=InnoDb');
        \DB::statement('ALTER TABLE cusines ENGINE=InnoDb');
        \DB::statement('ALTER TABLE ingredients ENGINE=InnoDb');
        \DB::statement('ALTER TABLE recipe_ingredients ENGINE=InnoDb');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(!DB::connection() instanceof MySqlConnection) {
            return;
        }
        
        \DB::statement('ALTER TABLE users ENGINE=MyISAM');
        \DB::statement('ALTER TABLE password_resets ENGINE=MyISAM');
        \DB::statement('ALTER TABLE recipes ENGINE=MyISAM');
        \DB::statement('ALTER TABLE courses ENGINE=MyISAM');
        \DB::statement('ALTER TABLE cusines ENGINE=MyISAM');
        \DB::statement('ALTER TABLE ingredients ENGINE=MyISAM');
        \DB::statement('ALTER TABLE recipe_ingredients ENGINE=MyISAM');
    }
}
