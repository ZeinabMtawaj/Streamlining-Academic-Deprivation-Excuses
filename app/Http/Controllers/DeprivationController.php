<?php


namespace App\Http\Controllers;

use App\Models\Deprivation;
use Illuminate\Http\Response;

class DeprivationController extends Controller
{
    public function export()
    {
        $user = auth()->user();

        $deprivations = Deprivation::with('course','excuse')
                ->where('student_id', $user->id) 
                ->get();
                
        $csvData = "Course, Initial Absence Percentage, Current Absence Percentage, Excuse, Status\n";
        foreach ($deprivations as $deprivation) {
            $csvData .= "\"" . ($deprivation->course->course_name ?? 'N/A') . "\",";
            $csvData .= "\"" . ($deprivation->initial_absence_percentage ?? 'N/A') . "%\",";
            $csvData .= "\"" . ($deprivation->current_absence_percentage ?? 'N/A') . "%\",";
            $csvData .= "\"" . ($deprivation->excuse ? 'Yes' : 'No') . "\",";
            $csvData .= "\"" . ($deprivation->status ?? 'N/A') . "\"\n";
        }

        return response($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="deprivations.csv"',
        ]);
    }
}
