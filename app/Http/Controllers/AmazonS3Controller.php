<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AmazonS3Controller extends Controller
{
    public function index()
    {
        $files = Storage::disk('s3')->files('uploads');
        $imageDetails = [];

        foreach ($files as $file) {
            $url = Storage::disk('s3')->url($file);
            $fileName = basename($file);
            $fileSize = Storage::disk('s3')->size($file);

            $imageDetails[] = [
                'url' => $url,
                'name' => $fileName,
                'size' => $fileSize,
            ];
        }

        return view('index', ['images' => $imageDetails]);
    }


    public function upload(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'images.*' => 'required|image|max:5120', // Maksimum dosya boyutu 5MB
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validatedData->errors()
            ], 422);
        }

        try {
            $uploadedImages = [];
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName();
                $path = 'uploads/' . $filename;
                $imageContent = file_get_contents($image->getRealPath());
                Storage::disk('s3')->put($path, $imageContent, 'public');
                $url = Storage::disk('s3')->url($path);
                $uploadedImages[] = $url;
            }

            return redirect()->back()->with('success', 'Image Upload Successful');
        } catch (\Exception $e) {
            Log::alert("AMAZON S3 UPLOAD ****** " . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred. The installation failed.');
        }
    }
}
