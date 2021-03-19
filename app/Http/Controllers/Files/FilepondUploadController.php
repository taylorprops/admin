<?php

namespace App\Http\Controllers\Files;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FilepondUploadController extends Controller {

    public function upload(Request $request) {

        // $file = $request -> agent_photo_file;

        // if ($file -> isValid()) {

        //     $filename = $file -> getClientOriginalName();
        //     $ext = $file -> extension();
        //     $filename = preg_replace('/\.'.$ext.'/i', '', $filename);
        //     $filename = time().'_'.sanitize($filename).'.'.$ext;
        //     $file -> storeAs('tmp', $filename, 'public');
        //     return response() -> json([
        //         'url' => Storage::disk('public') -> url('/tmp/'.$filename)
        //         ]);
        // }

    }

    public function cropped_upload(Request $request) {

        dd($request -> all());

    }

}
