<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Excuse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;


class FileController extends Controller
{
    public function download($filename)
    {
        $path = public_path('uploads\files\\' . $filename);
        // dd($path);



        if (!file_exists($path)) {
            abort(404);
        }

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename' => $filename
        ];
    
        return Response::download($path, $filename, $headers);
    }


  

    public function downloadPrivate(Request $request, $model, $folder, $id)
    {
        
        // Resolve the model class from the provided identifier
        $modelClass = $this->getModelClass($model);
    
        // Find the file record in the model by ID
        $fileRecord = $modelClass::findOrFail($id);

        if ($folder == "excuses"){
            $path = $fileRecord->excuse_file_path;
        }else{
            $path = $fileRecord->rejection_reason_file_path;
        }

        if (strpos($path, "{$folder}/") !== 0) {

            abort(404, 'File not found in the specified folder.');
        }
        $filePath = $path;


      
    
            $fileContent = Storage::disk('private')->get($filePath);
            $mimeType = Storage::disk('private')->mimeType($filePath);
            $fileName = basename($filePath);
    
            return response($fileContent, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);

    }
    

protected function getModelClass($modelIdentifier)
{
    // Map model identifiers to their respective model class namespaces
    $models = [
        'excuse' => Excuse::class,
    ];

    return $models[$modelIdentifier] ?? abort(404, 'Model not found.');
}






}
