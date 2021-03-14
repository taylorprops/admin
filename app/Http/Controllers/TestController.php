<?php

namespace App\Http\Controllers;

use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function test(Request $request) {
        $upload = Upload::get();

        foreach ($upload as $upload) {
            $file_location = Storage::disk('public') -> path(str_replace('/storage/', '', $upload -> file_location));
            $page_width = get_width_height($file_location)['width'];
            $page_height = get_width_height($file_location)['height'];
            $page_size = null;
            if ($page_width == 612 && $page_height == 792) {
                $page_size = 'letter';
            } elseif ($page_width == 595 && $page_height == 842) {
                $page_size = 'a4';
            }
            $upload -> page_width = $page_width;
            $upload -> page_height = $page_height;
            $upload -> page_size = $page_size;
            $upload -> save();

            $update_docs = TransactionDocuments::where('orig_file_id', $upload -> file_id) -> update(['page_width' => $page_width, 'page_height' => $page_height, 'page_size' => $page_size]);
            //$update_docs = TransactionDocuments::where('orig_file_id', $upload -> file_id) -> get();

            $update_uploads = TransactionUpload::where('orig_file_id', $upload -> file_id) -> update(['page_width' => $page_width, 'page_height' => $page_height, 'page_size' => $page_size]);
            //$update_uploads = TransactionUpload::where('orig_file_id', $upload -> file_id) -> get();
        }

        return view('/tests/test');
    }
}
