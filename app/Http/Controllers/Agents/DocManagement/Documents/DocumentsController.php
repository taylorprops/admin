<?php

namespace App\Http\Controllers\Agents\DocManagement\Documents;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\ResourceItems;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function documents(Request $request)
    {
        $custom_form_group_id = ResourceItems::GetResourceID('Custom', 'form_groups');
        $non_form_items_form_group_id = ResourceItems::GetResourceID('Non Form Items', 'form_groups');

        $form_groups = ResourceItems::where('resource_type', 'form_groups')
            ->whereNotIn('resource_id', [$custom_form_group_id, $non_form_items_form_group_id])
            ->orderBy('resource_order')
            ->get();

        return view('/agents/doc_management/documents/documents_html', compact('form_groups'));
    }

    public function get_form_group_files(Request $request)
    {
        $form_group_id = $request->form_group_id;

        $custom_form_group_id = ResourceItems::GetResourceID('Custom', 'form_groups');
        $non_form_items_form_group_id = ResourceItems::GetResourceID('Non Form Items', 'form_groups');

        $form_groups = ResourceItems::where('resource_type', 'form_groups')
            ->where(function ($query) use ($form_group_id) {
                if ($form_group_id > 0) {
                    $query->where('resource_id', $form_group_id);
                }
            })
            ->whereNotIn('resource_id', [$custom_form_group_id, $non_form_items_form_group_id])
            ->with('uploads:form_group_id,file_name_display,file_location,form_categories,state,helper_text')
            ->orderBy('resource_order')
            ->get();

        return view('/agents/doc_management/documents/get_form_group_files_html', compact('form_groups'));
    }
}
