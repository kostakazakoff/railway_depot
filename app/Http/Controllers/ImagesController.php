<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class ImagesController extends Controller
{
    
    public function delete(Request $request): JsonResponse
    {
        $imageIds = $request->all();

        $success = false;

        foreach ($imageIds as $id) {

            $image = Image::find($id);

            if ($image) {
                $imagePath = $image->path;
                $image->delete();
                File::delete($imagePath);
                $success = true;
            }
        }

        return response()->json(['success' => $success]);
    }
}
