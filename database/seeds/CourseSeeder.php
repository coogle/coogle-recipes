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
    ];
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\DB::table('courses')->truncate();
        
        foreach($this->_courses as $course) {
            $courseObj = new Course();
            $courseObj->name = $course;
            $courseObj->save();
        }
    }
}
