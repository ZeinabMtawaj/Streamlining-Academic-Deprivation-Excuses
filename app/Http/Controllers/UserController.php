<?php


namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenAdmin\Admin\Auth\Database\Administrator;


class UserController extends Controller
{

  
    public function logout(Request $request){
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
        
    }

    public function home(){

        $user = auth()->user(); // Get the authenticated user


        if ($user->roles->where('slug', 'student')->isNotEmpty()){
            return redirect('/students/home');
        }
        if ($user->roles->where('slug', 'advisor')->isNotEmpty()){
            return redirect('/advisors/home');
        }
        if ($user->roles->where('slug', 'committee-member')->isNotEmpty()){
            return redirect('/committees/home/Approved');
        }

    }


    public function signin(){
        return view('users.sign');
    }

    public function authenticate(Request $request){
        $formFields = $request->validate(
            [
                'academic_number' => ['required'],
                'password' => ['required'],
            ]);
        if (auth()->attempt($formFields)){
            $request->session()->regenerate();
            $user = auth()->user(); // Get the authenticated user
            if ($user->roles->where('slug', 'student')->isNotEmpty()){
                return redirect('/students/home');
            }
            if ($user->roles->where('slug', 'advisor')->isNotEmpty()){
                return redirect('/advisors/home');
            }
            if ($user->roles->where('slug', 'committee-member')->isNotEmpty()){
                return redirect('/committees/home/Approved');
            }

        }

        return back()->withErrors(
            [
                'invalidCred' => 'invalid credentials'

            ])->withInput();

       



    }


 
   


    public function getTeachersForStudent(Request $request)
    {
        $studentId = $request->input('query'); // assuming the input name is 'query'
        $student = Administrator::find($studentId);
        if (!$student) {
            return response()->json([]);
        }

        $roleModel = config('admin.database.roles_model');
        $role2 = $roleModel::where('slug', 'faculty-member')->firstOrFail(); 
        $facultyId = $student->faculty_id;

        $teachers = $role2->teacherUsers()->where('faculty_id', $facultyId)->get();

        $formattedTeachers = $teachers->map(function ($teacher) {
            return ['id' => $teacher->id, 'text' => $teacher->name];
        });
    
        return response()->json($formattedTeachers);
    
        // return response()->json($teachers);
    }

}