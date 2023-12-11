<?php


namespace App\Http\Controllers;

use App\Models\Excuse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AdvisorStudentLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class CommitteeController extends Controller
{
    public function index($stat){
        $user = auth()->user();
        // dd($stat);


        // Fetch excuses where the deprivation's student_id is in the list of student IDs
        $excuses = Excuse::with('deprivation.course', 'deprivation.student')
            ->where('advisor_decision',$stat)
            ->get();


        return view('committees.home',[
            'excuses' => $excuses,
            'stat' => $stat,
        ]);
    }

    public function export($stat){
        
    {
        $user = auth()->user();


        // Fetch excuses where the deprivation's student_id is in the list of student IDs
        $excuses = Excuse::with('deprivation.course', 'deprivation.student')
            ->where('advisor_decision',$stat)
            ->get();

        $csvData = "Student Name, Student Number, Course, Initial Absence Percentage, AdvsiorDecision";
        if($stat == "Approved")
            $csvData = $csvData.", CommitteeDecision, Current Absence Percentage, Status Of Deprivation\n";
        else
            $csvData = $csvData."\n";

        foreach ($excuses as $excuse) {
            $csvData .= "\"" . ($excuse->deprivation->student->name ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->student->academic_number ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->course->course_name ?? 'N/A') . "\",";
            $csvData .= "\"" . ($excuse->deprivation->initial_absence_percentage ?? 'N/A') . "%\",";
            $csvData .= "\"" . ($excuse->advisor_decision ?? 'N/A');
            if($stat == "Approved"){
                $csvData .= "\","."\"" . ($excuse->committee_decision ?? 'N/A') . "\",";
                $csvData .= "\"" . ($excuse->deprivation->current_absence_percentage ?? 'N/A') . "%\",";
                $csvData .= "\"" . ($excuse->deprivation->status ?? 'N/A') . "\",";
            }
            else
                $csvData .= "\"\n";

        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="excuses.csv"',
        ]);
    }

    }

    public function updateExcuse(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|integer|exists:excuses,id',
            'status' => 'required',
            'file' => Rule::requiredIf(function () use ($request) {
                return $request->status == "false";
            }),
            'current_absence_percentage' => Rule::requiredIf(function () use ($request) {
                return $request->status == "true";
            }),
        ]);
        $excuse = Excuse::find($validatedData['id']);
        if ($validatedData['status'] == "true")
            $excuse->committee_decision =  "Approved";
        else
            $excuse->committee_decision =  "Rejected";






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
            $excuse->committee_decision =  "Rejected";  

            $excuse->final_decision = "Rejected";

            if ($excuse->deprivation) {
                $excuse->deprivation->status = "Rejected";
                $excuse->deprivation->current_absence_percentage = $excuse->deprivation->initial_absence_percentage;        

                $excuse->deprivation->save(); // Save the related model
            }  

    } else {

        $excuse->committee_decision =  "Approved";  
        $excuse->final_decision = "Approved";
        if($excuse->rejection_reason_file_path)
            $excuse->rejection_reason_file_path = null;


        if ($excuse->deprivation) {
            $excuse->deprivation->current_absence_percentage= $validatedData['current_absence_percentage'];
            if($validatedData['current_absence_percentage'] < 25){
                $excuse->deprivation->status = "Approved";
            }
            else {
                $excuse->deprivation->status = "Rejected";
            }
            $excuse->deprivation->save();
        }      

        
    }

    $excuse->save();

    // Return a JSON response
    return response()->json(['message' => 'Your Excuse updated successfully.']);
    }

  
}