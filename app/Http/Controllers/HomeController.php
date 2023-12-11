<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Excuse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class HomeController extends Controller
{

    public function index(){

        $user = auth()->user(); // Get the authenticated user
        // dd($user->roles);
        if ($user->roles->where('slug', 'student')->isNotEmpty()){
            return view('students.home');
        }

    }

}