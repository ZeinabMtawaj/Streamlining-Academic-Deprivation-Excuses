<?php


namespace App\Http\Controllers;

use App\Models\Excuse;
use Illuminate\Http\Request;
use App\Models\AdvisorStudentLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class AdvisorController extends Controller
{
    public function index(){
        $user = auth()->user();
        $studentIds = AdvisorStudentLink::where('advisor_id', $user->id)
        ->pluck('student_id');

        // Fetch excuses where the deprivation's student_id is in the list of student IDs
        $excuses = Excuse::with('deprivation.course', 'deprivation.student')
            ->whereHas('deprivation', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->get();


        return view('advisors.home',[
            'excuses' => $excuses
        ]);
    }
}