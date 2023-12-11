<?php 

namespace App\Admin\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenAdmin\Admin\Controllers\AdminController;

class ImportController extends AdminController
{
    public function importData(Request $request)
    {
        $validatedData = $request->validate([
            'import_file' => 'required|file',
            'table_name' => 'required|string',
        ]);

        $file = $request->file('import_file');
        $tableName = $request->input('table_name');

        // Check which table to import data into
        switch ($tableName) {
            case config('admin.database.users_table'):
                $this->importUsers($file, $tableName );
                break;
            case config('admin.database.roles_table'):
                $this->importRoles($file, $tableName);
                break;
             case "academic_year_and_semester":
                $this->importAcademicYearAndSemester($file, $tableName);
                break;
             case "courses":
                $this->importCourses($file, $tableName);
                break;
             case "advisor_student_link":
                $this->importAdvisorStudentLinks($file, $tableName);
                break;
            case "deprivations":
                $this->importDeprivations($file, $tableName);
                break;
            case "excuses":
                $this->importExcuses($file, $tableName);
                break;

            // Add more cases for other tables
        }

        return redirect()->back();
    }

    private function importUsers($file, $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(['username', 'password', 'name', 'academic_number', 'role_name']);
    
        foreach ($records as $record) {
            if (empty($record['username'])) {
                continue;
            }
    
            $academicNumber = null;
            // if (!empty($record['academic_number']) && is_numeric($record['academic_number'])) {
            //     $academicNumber = (int)$record['academic_number'];
            // }
            $academicNumberValidation = Validator::make($record, [
                'academic_number' => ['required', 'numeric', 'max:999999999']
            ]);

            if ($academicNumberValidation->fails()) {
                // Handle validation failure for academic_number
                continue;
            }

            $academicNumber = $academicNumberValidation->validated()['academic_number'];

            $existingUser = DB::table($tableName)
            ->where('academic_number', $academicNumber)
            ->where('username', '!=', $record['username'])
            ->exists();

            if ($existingUser) {
                // Handle case where academic_number is not unique
                continue;
            }

            // Validate password
            $passwordValidation = Validator::make($record, [
                'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/']
            ]);

            if ($passwordValidation->fails()) {
                // Handle validation failure for password
                continue;
            }

            $hashedPassword = Hash::make($passwordValidation->validated()['password']);

    
            DB::table($tableName)->updateOrInsert(
                ['username' => $record['username']],
                [
                    'password' => $hashedPassword,
                    'academic_number' => $academicNumber,
                    'name' => $record['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    
            $user = DB::table($tableName)->where('username', $record['username'])->first();
            $userId = $user ? $user->id : null;
    
            // Use role_name to find the role_id
            $roleName = $record['role_name'];
            $role = DB::table('admin_roles')->where('name', $roleName)->first();
            $roleId = $role ? $role->id : null;
    
            if (!$roleId) {
                // Handle the case where the role doesn't exist
                continue;
            } else {
                DB::table('admin_role_users')->updateOrInsert(
                    [
                        'user_id' => $userId, // The conditions to find the record
                        'role_id' => $roleId
                    ],
                    [
                        'created_at' => now(), // The values to update or insert
                        'updated_at' => now()
                    ]
                );
                
            }
        }
    }
    
    private function importRoles($file, $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers
        $records = $csv->getRecords(['slug', 'name']);

        foreach ($records as $record) {
                $name = $record['name'];
                $slug = $record['slug'];
                if($name == "")
                    continue;
        
                // Use updateOrInsert to avoid duplicates
                DB::table($tableName)->updateOrInsert(
                    ['name' => $name, 
                ],
                    [
                        'slug' => $slug,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
    }

    private function importAcademicYearAndSemester($file,  $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers

        $records = $csv->getRecords(['academic_year', 'semester']);
        foreach ($records as $record) {
            // Validate or transform data as necessary
            $academicYear = isset($record['academic_year']) ? (int)$record['academic_year'] : null;
            $semester = isset($record['semester']) ? (int)$record['semester'] : null;

            // Insert or update logic
            DB::table( $tableName)->insert([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    private function importCourses($file,  $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers

        $records = $csv->getRecords(['course_name', 'year_semester_id']);
        foreach ($records as $record) {
            // Validate or transform data as necessary
            $courseName = $record['course_name'] ?? null;
            $yearSemesterId = isset($record['year_semester_id']) ? (int)$record['year_semester_id'] : null;

            // Ensure the year_semester_id exists in the academic_year_and_semester table
            $exists = DB::table('academic_year_and_semester')->where('id', $yearSemesterId)->exists();
            if (!$exists) {
                // Handle the case where the year_semester_id doesn't exist, maybe log an error or skip
                continue;
            }

            // Insert or update logic
            DB::table( $tableName)->insert([
                'course_name' => $courseName,
                'year_semester_id' => $yearSemesterId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function importAdvisorStudentLinks($file,  $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers

        $records = $csv->getRecords(['advisor_id', 'student_id']);
        foreach ($records as $record) {
            // Validate or transform data as necessary
            $advisorId = isset($record['advisor_id']) ? (int)$record['advisor_id'] : null;
            $studentId = isset($record['student_id']) ? (int)$record['student_id'] : null;

            // Ensure the advisor_id and student_id exist in the admin_users table
            $advisorExists = DB::table('admin_users')->where('id', $advisorId)->exists();
            $studentExists = DB::table('admin_users')->where('id', $studentId)->exists();
            if (!$advisorExists || !$studentExists) {
                // Handle the case where either advisor_id or student_id doesn't exist, maybe log an error or skip
                continue;
            }

            // Insert or update logic
            DB::table( $tableName)->insert([
                'advisor_id' => $advisorId,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function importDeprivations($file, $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers

        $records = $csv->getRecords(['student_id', 'course_id', 'initial_absence_percentage', 'current_absence_percentage', 'status']);
        foreach ($records as $record) {
            // Validate or transform data as necessary
            $studentId = isset($record['student_id']) ? (int)$record['student_id'] : null;
            $courseId = isset($record['course_id']) ? (int)$record['course_id'] : null;
            $initialAbsencePercentage = $record['initial_absence_percentage'] ?? null;
            $currentAbsencePercentage = $record['current_absence_percentage'] ?? null;
            $status = $record['status'] ?? null;

            // Ensure the student_id exists in the admin_users table and course_id exists in the courses table
            $studentExists = DB::table('admin_users')->where('id', $studentId)->exists();
            $courseExists = DB::table('courses')->where('id', $courseId)->exists();
            if (!$studentExists || !$courseExists) {
                // Handle the case where either student_id or course_id doesn't exist
                continue;
            }

            // Insert or update logic
            DB::table($tableName)->insert([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'initial_absence_percentage' => $initialAbsencePercentage,
                'current_absence_percentage' => $currentAbsencePercentage,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function importExcuses($file, $tableName)
    {
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Assumes the first row in CSV file is headers

        $records = $csv->getRecords(['excuse_file_path', 'deprivation_id', 'advisor_decision', 'committee_decision', 'final_decision', 'rejection_reason_file_path']);
        foreach ($records as $record) {
            // Validate or transform data as necessary
            $excuseFilePath = $record['excuse_file_path'] ?? null;
            $deprivationId = isset($record['deprivation_id']) ? (int)$record['deprivation_id'] : null;
            $advisorDecision = $record['advisor_decision'] ?? null;
            $committeeDecision = $record['committee_decision'] ?? null;
            $finalDecision = $record['final_decision'] ?? null;
            $rejectionReasonFilePath = $record['rejection_reason_file_path'] ?? null;

            // Ensure the deprivation_id exists in the deprivations table
            $deprivationExists = DB::table('deprivations')->where('id', $deprivationId)->exists();
            if (!$deprivationExists) {
                // Handle the case where deprivation_id doesn't exist
                continue;
            }

            // Insert or update logic
            DB::table($tableName)->insert([
                'excuse_file_path' => $excuseFilePath,
                'deprivation_id' => $deprivationId,
                'advisor_decision' => $advisorDecision,
                'committee_decision' => $committeeDecision,
                'final_decision' => $finalDecision,
                'rejection_reason_file_path' => $rejectionReasonFilePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }







}


