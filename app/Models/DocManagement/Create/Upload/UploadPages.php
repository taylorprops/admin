<?php

namespace App\Models\DocManagement\Create\Upload;

use Illuminate\Database\Eloquent\Model;

class UploadPages extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_create_uploads_pages';
    protected $guarded = [];
}
