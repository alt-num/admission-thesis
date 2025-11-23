<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Campus;
use App\Models\Department;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function courses()
    {
        $courses = Course::with('department')->orderBy('course_name')->paginate(20);

        return view('admission.courses.index', compact('courses'));
    }

    /**
     * Display a listing of campuses.
     */
    public function campuses()
    {
        $campuses = Campus::orderBy('campus_name')->paginate(20);

        return view('admission.campuses.index', compact('campuses'));
    }

    /**
     * Display a listing of departments.
     */
    public function departments()
    {
        $departments = Department::orderBy('department_name')->paginate(20);

        return view('admission.departments.index', compact('departments'));
    }
}

