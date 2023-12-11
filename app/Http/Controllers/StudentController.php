<?php


namespace App\Http\Controllers;

use App\Models\Deprivation;
use Illuminate\Http\Request;
use App\Models\Excuse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class StudentController extends Controller
{
    public function index(){
        $user = auth()->user();

        $deprivations = Deprivation::with('course','excuse')
                ->where('student_id', $user->id) 
                ->get();
        return view('students.home',[
            'deprivations' => $deprivations
        ]);
    }
}