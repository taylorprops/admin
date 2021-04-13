<?php

namespace App\Models\DocManagement\Create\Upload;

use Illuminate\Database\Eloquent\Model;

class UploadImages extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_create_uploads_images';
    protected $guarded = [];
}
