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
        'French'
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\DB::table('cusines')->truncate();
        
        foreach($this->_cusines as $cusine) {
            $cusineObj = new Cusine();
            $cusineObj->name = $cusine;
            $cusineObj->save();
        }
    }
}
