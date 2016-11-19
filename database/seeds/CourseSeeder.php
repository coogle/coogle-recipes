<?php

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    protected $_courses = [
        'Breakfast',
        'Lunch',
        'Dinner',
        'Snack',
        'Appetizer',
        'Brunch',
        'Entree',
        'Desert',
        'Other',
        'Side Dish'
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET foreign_key_checks=0');
        \DB::table('courses')->truncate();
        \DB::statement('SET foreign_key_checks=1');
        
        foreach($this->_courses as $course) {
            $courseObj = new Course();
            $courseObj->name = $course;
            $courseObj->save();
        }
    }
}
