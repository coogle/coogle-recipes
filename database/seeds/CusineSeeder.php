<?php

use Illuminate\Database\Seeder;
use App\Models\Cusine;

class CusineSeeder extends Seeder
{
    protected $_cusines = [
        'American',
        'Mexican',
        'Japanese',
        'Thai',
        'Italian',
        'Greek',
        'Mediterranean',
        'Chinese',
        'German',
        'Indian',
        'Korean',
        'French',
        'Other'
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET foreign_key_checks=0');
        \DB::table('cusines')->truncate();
        \DB::statement('SET foreign_key_checks=1');
        
        foreach($this->_cusines as $cusine) {
            $cusineObj = new Cusine();
            $cusineObj->name = $cusine;
            $cusineObj->save();
        }
    }
}
