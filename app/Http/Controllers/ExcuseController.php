<?php


namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Excuse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Models\AdvisorStudentLink;
use Illuminate\Support\Facades\Storage;
use OpenAdmin\Admin\Auth\Database\Administrator;


class ExcuseController extends Controller
{

    // public function get()
    // {
    //     $user = auth()->user(); // Get the authenticated user
    //     // dd($user->roles);
    //     if ($user->roles->where('slug', 'student')->isNotEmpty()){
    //         $courseApologyExcuses = CourseApologyExcuse::with('course')
    //             ->where('student_id', $user->id) 
    //             ->get();
    //     }
    //     if ($user->roles->where('slug', 'faculty-member')->isNotEmpty()){
    //         $courseApologyExcuses = CourseApologyExcuse::with('course')
    //         ->whereHas('course', function ($query) use ($user) {
    //             // This assumes that the 'courses' table has a 'faculty_id' column
    //             $query->where('faculty_id', $user->faculty_id);
    //         })
    //         ->get();
    //     }


    //     $cols = ["Course", "File", "Date", "Status", "Created at"];

    //     return view('courseApologyExcuses.read', [
    //         'data' => $courseApologyExcuses,
    //         'cols' => $cols
    //     ]);
    // }

    

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'course_id' => 'required|exists:courses,id',
    //         'file' => 'required|file',
    //         'date' => 'required|date',
    //     ]);

    //     $courseApologyExcuse = new CourseApologyExcuse();
    //     $courseApologyExcuse->student_id = auth()->id();
    //     $courseApologyExcuse->course_id = $request->course_id;
    //     $courseApologyExcuse->date = $request->date;

    //     if ($request->hasFile('file')) {
           
    //         $originalName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
    //         $extension = $request->file('file')->getClientOriginalExtension();
    
    //         // Sanitize the file name
    //         $safeName = Str::slug($originalName);
    
    //         // Make the filename unique by appending a number if it already exists
    //         $counter = 1;
    //         $filename = $safeName . '.' . $extension;
    
    //         // Check if file exists and append counter number to filename
    //         while (Storage::disk('private')->exists('course abologies/' . $filename)) {
    //             $filename = $safeName . '-' . $counter . '.' . $extension;
    //             $counter++;
    //         }
    
    //         // Store the file using the 'private' disk
    //         $filePath = $request->file('file')->storeAs('course abologies', $filename, 'private');
    
    //         // Set the file_path on the model
    //         $courseApologyExcuse->file_path = $filePath;
    
    
    //     }
    //     $courseApologyExcuse->save();

    //     return redirect()->route('courseApologyExcuses.read')->with('success', 'course apology excuse added successfully.');
    // }


    
    public function store(Request $request){
        
        // Validate the request
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:deprivations,id',
            'file' => 'required',
        ]);

        
        $folder_name = $request->folder_name;
        $excuse = Excuse::where('deprivation_id', $validatedData['id'])->first();
        if(!$excuse){

        $excuse = new Excuse();
        }

        $user = auth()->user(); // Get the authenticated user


        // // Check if the user is authorized to update the status
        if ($user->roles->where('slug', 'student')->isEmpty()){
            abort(403, 'Unauthorized action.');
        }

        // $excuse->status = "Pending";
        $excuse->deprivation_id = $validatedData['id'];
        
           
        $originalName = pathinfo($validatedData['file']->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $validatedData['file']->getClientOriginalExtension();

        // Sanitize the file name
        $safeName = Str::slug($originalName);

        // Make the filename unique by appending a number if it already exists
        $counter = 1;
        $filename = $safeName . '.' . $extension;

        // Check if file exists and append counter number to filename
        while (Storage::disk('private')->exists($folder_name.'/' . $filename)) {
            $filename = $safeName . '-' . $counter . '.' . $extension;
            $counter++;
        }

        // Store the file using the 'private' disk
        $filePath = $validatedData['file']->storeAs($folder_name, $filename, 'private');

        // Set the file_path on the model
        if ($folder_name == "excuses" )
            $excuse->excuse_file_path = $filePath;
        if($folder_name == "academic file")
        {
            $excuse->academic_file = $filePath;
        }
        if ($folder_name == "absence")
            $excuse->absence = $filePath;

        $excuse->save();

        // Return a JSON response
        return response()->json(['message' => 'Your Excuse sent successfully.']);
    }

    public function update(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:excuses,id',
            'status' => 'required',
            'file' => Rule::requiredIf(function () use ($request) {
                return $request->status == false;
            }),
            'rejection_reason' => Rule::requiredIf(function () use ($request) {
                return $request->status == false;
            }),
        ]);
        $excuse = Excuse::find($validatedData['id']);
        if ($validatedData['status'])
            $excuse->advisor_decision =  "Approved";
        if ( $excuse->rejection_reason_file_path){
            $excuse->rejection_reason_file_path = null;
            $excuse->rejection_reason = null;
        }





        if ($request->hasFile('file')) {

              
            $originalName = pathinfo($validatedData['file']->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $validatedData['file']->getClientOriginalExtension();

            // Sanitize the file name
            $safeName = Str::slug($originalName);

            // Make the filename unique by appending a number if it already exists
            $counter = 1;
            $filename = $safeName . '.' . $extension;

            // Check if file exists and append counter number to filename
            while (Storage::disk('private')->exists('rejection reason files/' . $filename)) {
                $filename = $safeName . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Store the file using the 'private' disk
            $filePath = $validatedData['file']->storeAs('rejection reason files', $filename, 'private');

            // Set the file_path on the model
            $excuse->rejection_reason_file_path = $filePath; 
            $excuse->rejection_reason = $validatedData['rejection_reason'];
            $excuse->final_decision = "Rejected";
            $excuse->advisor_decision =  "Rejected";  

            if ($excuse->deprivation) {
                $excuse->deprivation->status = "Rejected";
                $excuse->deprivation->save(); // Save the related model
            }          

    }

    $excuse->save();

    // Return a JSON response
    return response()->json(['message' => 'Your Excuse updated successfully.']);
    }

    public function export()
    {
        $user = auth()->user();
        $studentIds = AdvisorStudentLink::where('advisor_id', $user->id)
        ->pluck('student_id');

        // Fetch excuses where the deprivation's student_id is in the list of student IDs
        $excuses = Excuse::with('deprivation.course', 'deprivation.student')
            ->whereHas('deprivation', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->get();

        $csvData = "Student Name, Student Number, Course, Initial Absence Percentage, Status\n";
        foreach ($excuses as $excuse) {
            $csvData .= "\"" . ($excuse->deprivation->student->name ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->student->academic_number ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->course->course_name ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->initial_absence_percentage ?? 'N/A') . "%\",";
            $csvData .= "\"" . ($excuse->advisor_decision ?? 'N/A') . "\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="excuses.csv"',
        ]);
    }
}



