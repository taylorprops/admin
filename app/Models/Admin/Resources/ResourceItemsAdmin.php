<?php

namespace App\Models\Admin\Resources;

use Illuminate\Database\Eloquent\Model;

class ResourceItemsAdmin extends Model
{
    protected $connection = 'mysql';
    public $table = 'admin_resource_items';
    protected $primaryKey = 'resource_id';
    protected $guarded = [];
    public $timestamps = false;

    public function scopeGetResourceName($query, $id)
    {
        if ($id) {
            $resource = $query->where('resource_id', $id)->first();

            return $resource->resource_name;
        }

        return false;
    }

    public function scopeGetResourceID($query, $name, $type)
    {
        if ($name) {
            $resource = $query->where('resource_name', $name)->where('resource_type', $type)->first();

            return $resource->resource_id;
        }

        return false;
    }

    public function scopeGetCategoryColor($query, $id)
    {
        $tags = $query->where('resource_id', $id)->first();

        return $tags->resource_color;
    }
}
