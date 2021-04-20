<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Cron\CronController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\Esign\EsignController;
use App\Http\Controllers\CRM\ContactsController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\Calendar\CalendarController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Files\FilepondUploadController;
use App\Http\Controllers\BugReports\BugReportsController;
use App\Http\Controllers\TextEditor\FileUploadController;
use App\Http\Controllers\OldDB\CommissionPaymentsController;
use App\Http\Controllers\DocManagement\Fill\FieldsController;
use App\Http\Controllers\DocManagement\Create\UploadController;
use App\Http\Controllers\Admin\Permissions\PermissionsController;
use App\Http\Controllers\DocManagement\Earnest\EarnestController;
use App\Http\Controllers\DocManagement\Resources\ResourcesController;
use App\Http\Controllers\Notifications\GlobalNotificationsController;
use App\Http\Controllers\Admin\Resources\ResourceItemsAdminController;
use App\Http\Controllers\DocManagement\Checklists\ChecklistsController;
use App\Http\Controllers\DocManagement\Commission\CommissionController;
use App\Http\Controllers\DocManagement\Review\DocumentReviewController;
use App\Http\Controllers\Agents\DocManagement\Documents\DocumentsController;
use App\Http\Controllers\DocManagement\Notifications\NotificationsController;
use App\Http\Controllers\Agents\DocManagement\Functions\GlobalFunctionsController;
use App\Http\Controllers\Agents\DocManagement\Transactions\TransactionsController;
use App\Http\Controllers\Agents\DocManagement\Transactions\Add\TransactionsAddController;
use App\Http\Controllers\Agents\DocManagement\Transactions\Details\TransactionsDetailsController;
use App\Http\Controllers\Agents\DocManagement\Transactions\EditFiles\TransactionsEditFilesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('/auth/login');
}) -> name('login'); */


Route::view('/', '/auth/login');
Route::view('/login', '/auth/login');
Route::view('login', '/auth/login');

Auth::routes();
Route::get('/logout', [LoginController::class, 'logout']);

Route::get('/register_employee/{email}', [EmployeesController::class, 'register_employee']);

Route::get('/dashboard', [DashboardController::class, 'dashboard']);

/********** Calendar ********/
Route::get('/calendar', [CalendarController::class, 'calendar']);
Route::get('/calendar_events', [CalendarController::class, 'calendar_events']);
Route::post('/calendar_update', [CalendarController::class, 'calendar_update']);
Route::post('/calendar_delete', [CalendarController::class, 'calendar_delete']);

/********** Search Routes ********/
Route::get('/search', [SearchController::class, 'search']);

/***** file upload ******/
Route::post('/filepond_upload', [FilepondUploadController::class, 'upload']);

/***** notifications ******/
Route::get('/notifications/get_notifications', [GlobalNotificationsController::class, 'get_notifications']);
Route::post('/notifications/mark_as_read', [GlobalNotificationsController::class, 'mark_as_read']);

Route::get('/dashboard/get_transactions', [DashboardController::class, 'get_transactions']);
Route::get('/dashboard/get_commissions', [DashboardController::class, 'get_commissions']);
Route::get('/dashboard/get_upcoming_closings', [DashboardController::class, 'get_upcoming_closings']);
Route::get('/dashboard/get_admin_todo', [DashboardController::class, 'get_admin_todo']);


/********** Email Routes ********/
// Send Emails
Route::post('/send_email', [EmailController::class, 'send_email']);


/************ Users************/
Route::get('/users', [UserController::class, 'get_users']);
Route::get('/users/user_profile', [UserController::class, 'user_profile']);
Route::post('/users/save_profile', [UserController::class, 'save_profile']);
Route::post('/users/save_cropped_upload', [UserController::class, 'save_cropped_upload']);
Route::post('/users/delete_photo', [UserController::class, 'delete_photo']);

/************ Bug reports ************/
Route::get('/bug_reports', [BugReportsController::class, 'bug_reports']);
Route::get('/bug_reports/view_bug_report/{id}', [BugReportsController::class, 'view_bug_report']);
Route::post('/bug_reports/submit_bug_report', [BugReportsController::class, 'submit_bug_report']);

// ######### ADMIN ONLY ##########//
Route::middleware(['admin']) -> group(function () {

    /* List of uploads */
    Route::get('/doc_management/create/upload/files', [UploadController::class, 'get_uploaded_files']) -> name('create.upload.files');
    /* Add fields page */
    Route::get('/doc_management/create/add_fields/{file_id}', [FieldsController::class, 'add_fields']);
    /* Resources | Add/remove associations, tags, etc. */
    Route::get('/doc_management/resources/resources', [ResourcesController::class, 'resources']);
    // Admin resources
    Route::get('/admin/resources/resources_admin', [ResourceItemsAdminController::class, 'resources_admin']);
    /* Resources | Common Fields */
    Route::get('/doc_management/resources/common_fields', [ResourcesController::class, 'common_fields']);
    /* Resources | Get Common Fields */
    Route::get('/doc_management/resources/get_common_fields', [ResourcesController::class, 'get_common_fields']);
    /* Resources | Save Add Common Fields */
    Route::post('/doc_management/resources/save_add_common_field', [ResourcesController::class, 'save_add_common_field']);
    /* Resources | Save Edit Common Fields */
    Route::post('/doc_management/resources/save_edit_common_field', [ResourcesController::class, 'save_edit_common_field']);
    /* Resources | Reorder Common Fields */
    Route::post('/doc_management/resources/reorder_common_fields', [ResourcesController::class, 'reorder_common_fields']);

    /* Checklists  */
    Route::get('/doc_management/checklists/{checklist_id?}/{checklist_location_id?}/{checklist_type?}', [ChecklistsController::class, 'checklists']);

    /****************** END COMPONENT PAGES: DOCUMENT MANAGEMENT ******************/

    //************************** COMPONENT DATA: DOCUMENT MANAGEMENT **************************//

    /**********  DATA - ADD/EDIT/DELETE /**********/

    // Upload //
    Route::post('/doc_management/upload_file', [UploadController::class, 'upload_file']) -> name('doc_management.upload_file');
    // Edit uploaded File
    Route::post('/doc_management/save_file_edit', [UploadController::class, 'save_file_edit']);
    // Add non form checklist item
    Route::post('/doc_management/save_add_non_form', [UploadController::class, 'save_add_non_form']);
    // Duplicate uploaded files
    Route::post('/doc_management/duplicate_upload', [UploadController::class, 'duplicate_upload']);
    // Activate/Deactivate uploaded files
    Route::post('/doc_management/activate_upload', [UploadController::class, 'activate_upload']);
    // Publish upload
    Route::post('/doc_management/publish_upload', [UploadController::class, 'publish_upload']);
    // Delete uploaded files
    Route::post('/doc_management/delete_upload', [UploadController::class, 'delete_upload']);
    // Manage uploaded files in checklists
    Route::post('/doc_management/manage_upload', [UploadController::class, 'manage_upload']);
    // Replace uploaded files in checklists
    Route::post('/doc_management/replace_upload', [UploadController::class, 'replace_upload']);
    // Remove uploaded files in checklists
    Route::post('/doc_management/remove_upload', [UploadController::class, 'remove_upload']);
    // get upload text
    Route::post('/doc_management/get_upload_text', [UploadController::class, 'get_upload_text']);

    /* Add Resource  */
    Route::post('/doc_management/resources/add', [ResourcesController::class, 'resources_add']);
    /* Save edit Resources | Add/remove associations, tags, etc. */
    Route::post('/doc_management/resources/edit', [ResourcesController::class, 'resources_edit']);
    /* Delete Resources  */
    Route::post('/doc_management/resources/delete_deactivate', [ResourcesController::class, 'delete_deactivate']);
    /* Reorder Resources */
    Route::post('/doc_management/resources/reorder', [ResourcesController::class, 'resources_reorder']);

    // ADMIN RESOURCES //
    /* Add Resource  */
    Route::post('/admin/resources/add', [ResourceItemsAdminController::class, 'resources_add']);
    /* Save edit Resources | Add/remove associations, tags, etc. */
    Route::post('/admin/resources/edit', [ResourceItemsAdminController::class, 'resources_edit']);
    /* Delete Resources  */
    Route::post('/admin/resources/delete_deactivate', [ResourceItemsAdminController::class, 'delete_deactivate']);
    /* Reorder Resources */
    Route::post('/admin/resources/reorder', [ResourceItemsAdminController::class, 'resources_reorder']);

    /* Permissions */
    // get permissions
    Route::get('/permissions/permissions', [PermissionsController::class, 'permissions']);
    // save permissions
    Route::post('/permissions/save_permissions', [PermissionsController::class, 'save_permissions']);
    // reorder permissions
    Route::post('/permissions/reorder_permissions', [NotificationsController::class, 'reorder_permissions']);


    /* Notifications */
    // get notifications
    Route::get('/doc_management/notifications', [NotificationsController::class, 'notifications']);
    // save notifications
    Route::post('/doc_management/save_notifications', [NotificationsController::class, 'save_notifications']);
    // reorder notifications
    Route::post('/doc_management/reorder_notifications', [NotificationsController::class, 'reorder_notifications']);

    // Fields //
    Route::post('/doc_management/save_add_fields', [FieldsController::class, 'save_add_fields']);
    // delete page from upload
    Route::post('/doc_management/delete_page', [FieldsController::class, 'delete_page']);

    /* checklists */
    /* Add Checklists */
    Route::post('/doc_management/add_checklist', [ChecklistsController::class, 'add_checklist']);
    /* Add Referral Checklists */
    Route::post('/doc_management/add_checklist_referral', [ChecklistsController::class, 'add_checklist_referral']);
    /* Edit Checklists */
    Route::post('/doc_management/edit_checklist', [ChecklistsController::class, 'edit_checklist']);
    /* Delete Checklists */
    Route::post('/doc_management/delete_checklist', [ChecklistsController::class, 'delete_checklist']);
    /* Reorder Checklists */
    Route::post('/doc_management/reorder_checklists', [ChecklistsController::class, 'reorder_checklists']);
    /* Add Checklist Items */
    Route::post('/doc_management/add_checklist_items', [ChecklistsController::class, 'add_checklist_items']);
    /* Add Form to Checklists  */
    Route::post('/doc_management/save_add_to_checklists', [UploadController::class, 'save_add_to_checklists']);
    /* Duplicate Checklist  */
    Route::post('/doc_management/duplicate_checklist', [ChecklistsController::class, 'duplicate_checklist']);
    /* Save Copy Checklists  */
    Route::post('/doc_management/save_copy_checklists', [ChecklistsController::class, 'save_copy_checklists']);

    /**********  DATA - GET /**********/

    // get updated list of form_group files after adding a new one
    Route::get('/doc_management/get_form_group_files', [UploadController::class, 'get_form_group_files']) -> name('doc_management.get_form_group_files');
    // get upload details for edit
    Route::get('/doc_management/get_upload_details', [UploadController::class, 'get_upload_details']);
    // get checklist after adding
    Route::get('/doc_management/get_checklists', [ChecklistsController::class, 'get_checklists']);
    /* Copy Checklists */
    Route::get('/doc_management/get_copy_checklists', [ChecklistsController::class, 'get_copy_checklists']);
    // get checklist items
    Route::get('/doc_management/get_checklist_items', [ChecklistsController::class, 'get_checklist_items']);
    // get checklist item details
    Route::get('/doc_management/get_checklist_item_details', [ChecklistsController::class, 'get_checklist_item_details']);
    // get details to manage form in checklist
    Route::get('/doc_management/get_manage_upload_details', [UploadController::class, 'get_manage_upload_details']);
    // get checklist items for add form to checklists
    Route::get('/doc_management/add_form_get_checklist_items', [UploadController::class, 'add_form_get_checklist_items']);
    // get details to add to checklists
    Route::get('/doc_management/get_add_to_checklists_details', [UploadController::class, 'get_add_to_checklists_details']);

    //Route::get('/doc_management/common_fields', [FieldsController::class, 'get_common_fields']);

    // get custom names for autofill when adding a form
    Route::get('/doc_management/get_custom_names', [FieldsController::class, 'get_custom_names']);
    // get edit properties modal
    Route::get('/doc_management/get_edit_properties_html', [FieldsController::class, 'get_edit_properties_html']);

    /********* Balance Earnest ************/
    // active earnest page
    Route::get('/doc_management/active_earnest', [EarnestController::class, 'active_earnest']);
    // active earnest page - get earnest deposits
    Route::get('/doc_management/get_earnest_deposits', [EarnestController::class, 'get_earnest_deposits']);
    // balance earnest page
    Route::get('/doc_management/balance_earnest', [EarnestController::class, 'balance_earnest']);
    // get earnest totals
    Route::get('/doc_management/get_earnest_totals', [EarnestController::class, 'get_earnest_totals']);
    // get earnest checks
    Route::get('/doc_management/get_earnest_checks', [EarnestController::class, 'get_earnest_checks']);
    // search earnest checks
    Route::get('/doc_management/search_earnest_checks', [EarnestController::class, 'search_earnest_checks']);
    // email agents missing earnest
    Route::post('/doc_management/email_agents_missing_earnest', [EarnestController::class, 'email_agents_missing_earnest']);

    /********* Document Review ************/
    // doc review page
    Route::get('/doc_management/document_review/{Contract_ID?}', [DocumentReviewController::class, 'document_review']);
    // get checklist
    Route::get('/doc_management/get_checklist', [DocumentReviewController::class, 'get_checklist']);
    // get docs
    Route::get('/doc_management/get_documents', [DocumentReviewController::class, 'get_documents']);
    // get details
    Route::get('/doc_management/get_details', [DocumentReviewController::class, 'get_details']);
    // save earnest and title details
    Route::post('/doc_management/save_earnest_and_title_details', [DocumentReviewController::class, 'save_earnest_and_title_details']);

    /********* Commission ************/
    // commission
    Route::get('/doc_management/commission', [CommissionController::class, 'commission']);
    // get commissions pending
    Route::get('/doc_management/commission/get_commissions_pending', [CommissionController::class, 'get_commissions_pending']);
    // get checks queue to add to commission
    Route::get('/doc_management/commission/get_checks_queue', [CommissionController::class, 'get_checks_queue']);
    // search deleted checks
    Route::get('/doc_management/commission/search_deleted_checks', [CommissionController::class, 'search_deleted_checks']);

    // commission page for checks with no property
    Route::get('/doc_management/commission_other/{Commission_ID}', [CommissionController::class, 'commission_other']);
    // get commission details
    Route::get('/doc_management/commission_other/commission_other_details/{Commission_ID}', [CommissionController::class, 'commission_other_details']);
    // save edit check in
    Route::post('/doc_management/commission/save_edit_queue_check', [CommissionController::class, 'save_edit_queue_check']);
    // make payment from commission
    Route::post('/doc_management/commission/make_payment_from_commission', [CommissionPaymentsController::class, 'make_payment_from_commission']);


    /**********  File review /**********/

    // accept reject checklist items
    Route::post('/agents/doc_management/transactions/set_checklist_item_review_status', [TransactionsDetailsController::class, 'set_checklist_item_review_status']);
    // mark checklist items required or if applicable
    Route::post('/agents/doc_management/transactions/mark_required', [TransactionsDetailsController::class, 'mark_required']);
    // save add checklist item
    Route::post('/agents/doc_management/transactions/save_add_checklist_item', [TransactionsDetailsController::class, 'save_add_checklist_item']);
    // remove checklist item
    Route::post('/agents/doc_management/transactions/remove_checklist_item', [TransactionsDetailsController::class, 'remove_checklist_item']);
    // get email checklist html
    Route::get('/agents/doc_management/transactions/get_email_checklist_html', [TransactionsDetailsController::class, 'get_email_checklist_html']);


    /************ Employees ************/
    Route::get('/employees', [EmployeesController::class, 'employees']);
    Route::get('/employees/get_employees', [EmployeesController::class, 'get_employees']);
    Route::get('/employees/get_users', [EmployeesController::class, 'get_users']);
    Route::post('/employees/save_employee', [EmployeesController::class, 'save_employee']);
    Route::post('/employees/save_cropped_upload', [EmployeesController::class, 'save_cropped_upload']);
    Route::post('/employees/delete_photo', [EmployeesController::class, 'delete_photo']);
    Route::post('/employees/docs_upload', [EmployeesController::class, 'docs_upload']);
    Route::get('/employees/get_docs', [EmployeesController::class, 'get_docs']);
    Route::post('/employees/delete_doc', [EmployeesController::class, 'delete_doc']);




    /************ Form Elements ************/
    /* Route::get('/form_elements', function() {
        return view('/tests/form_elements');
    }); */

    // Test Controller
    Route::get('/tests/test', [TestController::class, 'test']);

});


// ***************************** AGENTS ********************************
Route::middleware(['agent']) -> group(function () {

    /********** Contacts ********/
    Route::get('/contacts', [ContactsController::class, 'contacts']);
    // get contacts
    Route::get('/contacts/get_contacts', [ContactsController::class, 'get_contacts']);
    // delete contacts
    Route::post('/contacts/delete', [ContactsController::class, 'delete']);
    // save add/edit contacts
    Route::post('/contacts/save', [ContactsController::class, 'save']);
    // import contacts from excel
    Route::post('/contacts/import_from_excel', [ContactsController::class, 'import_from_excel']);

    // Global functions
    Route::get('/agents/doc_management/global_functions/get_location_details', [GlobalFunctionsController::class, 'get_location_details']);

    // tinymce file upload
    Route::post('/text_editor/file_upload', [FileUploadController::class, 'file_upload']);

    // all transactions page
    Route::get('/agents/doc_management/transactions', [TransactionsController::class, 'transactions_all']);
    // get transactions
    Route::get('/agents/doc_management/transactions/get_transactions', [TransactionsController::class, 'get_transactions']);

    // Add new transaction
    Route::get('/agents/doc_management/transactions/add/{type}', [TransactionsAddController::class, 'add_transaction']);
    // Add listing details if existing
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_existing/{Agent_ID}/{transaction_type}/{state?}/{tax_id?}/{bright_type?}/{bright_id?}', [TransactionsAddController::class, 'transaction_add_details_existing']);
    // Add listing details if new
    Route::get('/agents/doc_management/transactions/add/transaction_add_details_new/{Agent_ID}/{transaction_type}/{street_number?}/{street_name?}/{city?}/{state?}/{zip?}/{county?}/{street_dir?}/{unit_number?}', [TransactionsAddController::class, 'transaction_add_details_new']);
    // Add transaction details if referral
    Route::post('/agents/doc_management/transactions/add/transaction_add_details_referral', [TransactionsAddController::class, 'transaction_add_details_referral']);
     // Save transaction details if referral
    Route::post('/agents/doc_management/transactions/add/transaction_save_details_referral', [TransactionsAddController::class, 'transaction_save_details_referral']);
    // Required Details page
    Route::get('/agents/doc_management/transactions/add/transaction_required_details/{id}/{transaction_type}', [TransactionsAddController::class, 'transaction_required_details']);
    // Required Details page referral
    Route::get('/agents/doc_management/transactions/add/transaction_required_details_referral/{Referral_ID}', [TransactionsAddController::class, 'transaction_required_details_referral']);

    // save add listing
    Route::post('/agents/doc_management/transactions/save_add_transaction', [TransactionsAddController::class, 'save_add_transaction']);
    // save required details
    Route::post('/agents/doc_management/transactions/save_transaction_required_details', [TransactionsAddController::class, 'save_transaction_required_details']);

    // listing details page
    Route::get('/agents/doc_management/transactions/transaction_details/{id}/{transaction_type}', [TransactionsDetailsController::class, 'transaction_details']);
    // get header for listing details page
    Route::get('/agents/doc_management/transactions/transaction_details_header', [TransactionsDetailsController::class, 'transaction_details_header']);

    // get details, members, checklist, etc for listing page
    Route::get('/agents/doc_management/transactions/get_details', [TransactionsDetailsController::class, 'get_details']);
    Route::get('/agents/doc_management/transactions/get_members', [TransactionsDetailsController::class, 'get_members']);
    Route::get('/agents/doc_management/transactions/get_checklist', [TransactionsDetailsController::class, 'get_checklist']);
    // get checklist notes
    Route::get('/doc_management/get_notes', [DocumentReviewController::class, 'get_notes']);
    Route::post('/doc_management/delete_note', [DocumentReviewController::class, 'delete_note']);
    Route::get('/agents/doc_management/transactions/get_documents', [TransactionsDetailsController::class, 'get_documents']);
    Route::get('/agents/doc_management/transactions/get_esign', [TransactionsDetailsController::class, 'get_esign']);
    Route::get('/agents/doc_management/transactions/get_contracts', [TransactionsDetailsController::class, 'get_contracts']);
    Route::post('/agents/doc_management/transactions/in_process', [TransactionsDetailsController::class, 'in_process']);
    Route::post('/agents/doc_management/transactions/in_process_esign', [TransactionsDetailsController::class, 'in_process_esign']);

    // get mls details
    Route::get('/agents/doc_management/transactions/mls_search', [TransactionsDetailsController::class, 'mls_search']);
    Route::get('/agents/doc_management/transactions/save_mls_search', [TransactionsDetailsController::class, 'save_mls_search']);
    // get add contact html
    Route::get('/agents/doc_management/transactions/add_member_html', [TransactionsDetailsController::class, 'add_member_html']);
    // save details
    Route::post('/agents/doc_management/transactions/save_details', [TransactionsDetailsController::class, 'save_details']);
    // save member
    Route::post('/agents/doc_management/transactions/save_member', [TransactionsDetailsController::class, 'save_member']);
    // delete member
    Route::post('/agents/doc_management/transactions/delete_member', [TransactionsDetailsController::class, 'delete_member']);
    // add documents folder
    Route::post('/agents/doc_management/transactions/add_folder', [TransactionsDetailsController::class, 'add_folder']);
    // delete documents folder
    Route::post('/agents/doc_management/transactions/delete_folder', [TransactionsDetailsController::class, 'delete_folder']);
    // upload documents
    Route::post('/agents/doc_management/transactions/upload_documents', [TransactionsDetailsController::class, 'upload_documents']);
    // save add template documents
    Route::post('/agents/doc_management/transactions/save_add_template_documents', [TransactionsDetailsController::class, 'save_add_template_documents']);
    // move documents to trash
    Route::post('/agents/doc_management/transactions/move_documents_to_trash', [TransactionsDetailsController::class, 'move_documents_to_trash']);
    // move documents to different folder
    Route::post('/agents/doc_management/transactions/move_documents_to_folder', [TransactionsDetailsController::class, 'move_documents_to_folder']);
    // reorder documents
    Route::post('/agents/doc_management/transactions/reorder_documents', [TransactionsDetailsController::class, 'reorder_documents']);
    // get add document to checklist html
    Route::get('/agents/doc_management/transactions/add_document_to_checklist_item_html', [TransactionsDetailsController::class, 'add_document_to_checklist_item_html']);
    // check if address submitted for release
    Route::post('/agents/doc_management/transactions/release_address_submitted', [TransactionsDetailsController::class, 'release_address_submitted']);
    // add address for release
    Route::post('/agents/doc_management/transactions/add_release_address', [TransactionsDetailsController::class, 'add_release_address']);
    // get documents for add document to checklist html
    Route::get('/agents/doc_management/transactions/get_add_document_to_checklist_documents_html', [TransactionsDetailsController::class, 'get_add_document_to_checklist_documents_html']);

    // get documents emailed
    Route::get('/agents/doc_management/transactions/get_emailed_documents', [TransactionsDetailsController::class, 'get_emailed_documents']);
    // add documents emailed
    Route::post('/agents/doc_management/transactions/add_emailed_documents', [TransactionsDetailsController::class, 'add_emailed_documents']);
    // delete document emailed
    Route::post('/agents/doc_management/transactions/delete_emailed_document', [TransactionsDetailsController::class, 'delete_emailed_document']);

    // delete document from checklist item
    Route::post('/agents/doc_management/transactions/remove_document_from_checklist_item', [TransactionsDetailsController::class, 'remove_document_from_checklist_item']);
    // add notes checklist item
    Route::post('/agents/doc_management/transactions/add_notes_to_checklist_item', [TransactionsDetailsController::class, 'add_notes_to_checklist_item']);
    // mark note read
    Route::post('/agents/doc_management/transactions/mark_note_read', [TransactionsDetailsController::class, 'mark_note_read']);

    // add one document to checklist item from checklist
    Route::post('/agents/doc_management/transactions/add_document_to_checklist_item', [TransactionsDetailsController::class, 'add_document_to_checklist_item']);
    // save assign items to checklist from documents
    Route::post('/agents/doc_management/transactions/save_assign_documents_to_checklist', [TransactionsDetailsController::class, 'save_assign_documents_to_checklist']);
    // change checklist
    Route::post('/agents/doc_management/transactions/change_checklist', [TransactionsDetailsController::class, 'change_checklist']);
    // save rename document
    Route::post('/agents/doc_management/transactions/save_rename_document', [TransactionsDetailsController::class, 'save_rename_document']);
    // get split document html
    Route::get('/agents/doc_management/transactions/get_split_document_html', [TransactionsDetailsController::class, 'get_split_document_html']);
    // save add split document to documents
    Route::post('/agents/doc_management/transactions/save_split_document', [TransactionsDetailsController::class, 'save_split_document']);
    // duplicate document
    Route::post('/agents/doc_management/transactions/duplicate_document', [TransactionsDetailsController::class, 'duplicate_document']);
    // get email documents
    Route::post('/agents/doc_management/transactions/email_get_documents', [TransactionsDetailsController::class, 'email_get_documents']);
    // email documents
    Route::post('/agents/doc_management/transactions/send_email', [TransactionsDetailsController::class, 'send_email']);
    // merge documents
    Route::post('/agents/doc_management/transactions/merge_documents', [TransactionsDetailsController::class, 'merge_documents']);

    // make sure all required fields are filled out before allowing adding documents to the checklist
    Route::post('/agents/doc_management/transactions/check_required_contract_fields', [TransactionsDetailsController::class, 'check_required_contract_fields']);
    // save required fields
    Route::post('/agents/doc_management/transactions/save_required_fields', [TransactionsDetailsController::class, 'save_required_fields']);

    /////// COMMISSION
    // get commission
    Route::get('/agents/doc_management/transactions/get_commission', [TransactionsDetailsController::class, 'get_commission']);
    // get agent commission
    Route::get('/agents/doc_management/transactions/get_agent_commission', [TransactionsDetailsController::class, 'get_agent_commission']);
    // get check details from pdf
    Route::post('/agents/doc_management/transactions/get_check_details', [TransactionsDetailsController::class, 'get_check_details']);

    // get commission notes
    Route::get('/agents/doc_management/transactions/get_commission_notes', [TransactionsDetailsController::class, 'get_commission_notes']);

    // save commission
    Route::post('/agents/doc_management/transactions/save_commission', [TransactionsDetailsController::class, 'save_commission']);
    // save commission agent
    Route::post('/agents/doc_management/transactions/save_commission_agent', [TransactionsDetailsController::class, 'save_commission_agent']);

    // get checks in
    Route::get('/agents/doc_management/transactions/get_checks_in', [TransactionsDetailsController::class, 'get_checks_in']);
    // get checks in queue
    Route::get('/agents/doc_management/transactions/get_checks_in_queue', [TransactionsDetailsController::class, 'get_checks_in_queue']);
    // save add check in
    Route::post('/agents/doc_management/transactions/save_add_check_in', [TransactionsDetailsController::class, 'save_add_check_in']);
    // delete check in
    Route::post('/agents/doc_management/transactions/save_delete_check_in', [TransactionsDetailsController::class, 'save_delete_check_in']);
    // undo delete check in
    Route::post('/agents/doc_management/transactions/undo_delete_check_in', [TransactionsDetailsController::class, 'undo_delete_check_in']);
    // save edit check in
    Route::post('/agents/doc_management/transactions/save_edit_check_in', [TransactionsDetailsController::class, 'save_edit_check_in']);
    // import check in
    Route::post('/agents/doc_management/transactions/import_check_in', [TransactionsDetailsController::class, 'import_check_in']);
    // send check in back to queue
    Route::post('/agents/doc_management/transactions/re_queue_check', [TransactionsDetailsController::class, 're_queue_check']);

    // get checks out
    Route::get('/agents/doc_management/transactions/get_checks_out', [TransactionsDetailsController::class, 'get_checks_out']);
    // save add check out
    Route::post('/agents/doc_management/transactions/save_add_check_out', [TransactionsDetailsController::class, 'save_add_check_out']);
    // delete check out
    Route::post('/agents/doc_management/transactions/save_delete_check_out', [TransactionsDetailsController::class, 'save_delete_check_out']);
    // undo delete check out
    Route::post('/agents/doc_management/transactions/undo_delete_check_out', [TransactionsDetailsController::class, 'undo_delete_check_out']);
    // save edit check out
    Route::post('/agents/doc_management/transactions/save_edit_check_out', [TransactionsDetailsController::class, 'save_edit_check_out']);

    // get check deductions
    Route::get('/agents/doc_management/transactions/get_income_deductions', [TransactionsDetailsController::class, 'get_income_deductions']);
    // save add check deduction
    Route::post('/agents/doc_management/transactions/save_add_income_deduction', [TransactionsDetailsController::class, 'save_add_income_deduction']);
    // delete check deduction
    Route::post('/agents/doc_management/transactions/delete_income_deduction', [TransactionsDetailsController::class, 'delete_income_deduction']);

    // get check deductions
    Route::get('/agents/doc_management/transactions/get_commission_deductions', [TransactionsDetailsController::class, 'get_commission_deductions']);
    // save add check deduction
    Route::post('/agents/doc_management/transactions/save_add_commission_deduction', [TransactionsDetailsController::class, 'save_add_commission_deduction']);
    // delete check deduction
    Route::post('/agents/doc_management/transactions/delete_commission_deduction', [TransactionsDetailsController::class, 'delete_commission_deduction']);
    // get commission notes html
    Route::get('/agents/doc_management/transactions/details/data/get_commission_notes_html', [TransactionsDetailsController::class, 'get_commission_notes_html']);
    // add commission notes
    Route::post('/agents/doc_management/transactions/add_commission_notes', [TransactionsDetailsController::class, 'add_commission_notes']);
    // get agent details
    Route::get('/agents/doc_management/transactions/details/data/get_agent_details', [TransactionsDetailsController::class, 'get_agent_details']);
    // get agent commission details
    Route::get('/agents/doc_management/transactions/details/data/get_agent_commission_details', [TransactionsDetailsController::class, 'get_agent_commission_details']);

    /////// Earnest ////////////////
    // get earnest
    Route::get('/agents/doc_management/transactions/get_earnest', [TransactionsDetailsController::class, 'get_earnest']);
    // get checks
    Route::get('/agents/doc_management/transactions/get_earnest_checks', [TransactionsDetailsController::class, 'get_earnest_checks']);
    // save earnest
    Route::post('/agents/doc_management/transactions/save_earnest', [TransactionsDetailsController::class, 'save_earnest']);
    // save earnest amounts
    Route::post('/agents/doc_management/transactions/save_earnest_amounts', [TransactionsDetailsController::class, 'save_earnest_amounts']);
    // save add earnest check
    Route::post('/agents/doc_management/transactions/save_add_earnest_check', [TransactionsDetailsController::class, 'save_add_earnest_check']);
    // save edit earnest check
    Route::post('/agents/doc_management/transactions/save_edit_earnest_check', [TransactionsDetailsController::class, 'save_edit_earnest_check']);
    // clear/bounce earnest check
    Route::post('/agents/doc_management/transactions/clear_bounce_earnest_check', [TransactionsDetailsController::class, 'clear_bounce_earnest_check']);
    // notify agent earnest check
    Route::post('/agents/doc_management/transactions/notify_agent_bounced_earnest', [TransactionsDetailsController::class, 'notify_agent_bounced_earnest']);
    // delete earnest check
    Route::post('/agents/doc_management/transactions/delete_earnest_check', [TransactionsDetailsController::class, 'delete_earnest_check']);
    // undo delete earnest check
    Route::post('/agents/doc_management/transactions/undo_delete_earnest_check', [TransactionsDetailsController::class, 'undo_delete_earnest_check']);
    // set status to waiting for release
    Route::post('/agents/doc_management/transactions/set_status_to_waiting_for_release', [TransactionsDetailsController::class, 'set_status_to_waiting_for_release']);
    // get notes
    Route::get('/agents/doc_management/transactions/get_earnest_notes', [TransactionsDetailsController::class, 'get_earnest_notes']);
    // add notes
    Route::post('/agents/doc_management/transactions/save_add_earnest_notes', [TransactionsDetailsController::class, 'save_add_earnest_notes']);
    // delete note
    Route::post('/agents/doc_management/transactions/delete_note', [TransactionsDetailsController::class, 'delete_note']);
    // transfer earnest
    Route::post('/agents/doc_management/transactions/transfer_earnest', [TransactionsDetailsController::class, 'transfer_earnest']);
    // undo transfer earnest
    Route::post('/agents/doc_management/transactions/undo_transfer_earnest', [TransactionsDetailsController::class, 'undo_transfer_earnest']);

    // Accept new contract for listing
    Route::post('/agents/doc_management/transactions/accept_contract', [TransactionsDetailsController::class, 'accept_contract']);
    // Release contract on listing
    Route::post('/agents/doc_management/transactions/cancel_contract', [TransactionsDetailsController::class, 'cancel_contract']);
    // UNDO release or canceled contract
    Route::post('/agents/doc_management/transactions/undo_cancel_contract', [TransactionsDetailsController::class, 'undo_cancel_contract']);
    // UNDO canceled listing
    Route::post('/agents/doc_management/transactions/undo_cancel_listing', [TransactionsDetailsController::class, 'undo_cancel_listing']);
    // check if docs submitted and accepted
    Route::get('/agents/doc_management/transactions/check_docs_submitted_and_accepted', [TransactionsDetailsController::class, 'check_docs_submitted_and_accepted']);
    // cancel listing
    Route::post('/agents/doc_management/transactions/cancel_listing', [TransactionsDetailsController::class, 'cancel_listing']);

    // cancel referral
    Route::post('/agents/doc_management/transactions/cancel_referral', [TransactionsDetailsController::class, 'cancel_referral']);
    // UNDO cancel referral
    Route::post('/agents/doc_management/transactions/undo_cancel_referral', [TransactionsDetailsController::class, 'undo_cancel_referral']);

    // merge listing with contract
    Route::get('/agents/doc_management/transactions/merge_listing_and_contract', [TransactionsDetailsController::class, 'merge_listing_and_contract']);

    // save merge listing with contract
    Route::post('/agents/doc_management/transactions/save_merge_listing_and_contract', [TransactionsDetailsController::class, 'save_merge_listing_and_contract']);

    // save undo merge listing with contract
    Route::post('/agents/doc_management/transactions/save_undo_merge_listing_and_contract', [TransactionsDetailsController::class, 'save_undo_merge_listing_and_contract']);

    // update contract status
    Route::post('/agents/doc_management/transactions/update_contract_status', [TransactionsDetailsController::class, 'update_contract_status']);

    // search bright mls agents
    Route::get('/agents/doc_management/transactions/search_bright_agents', [TransactionsDetailsController::class, 'search_bright_agents']);

    Route::get('/agents/doc_management/transactions/get_property_info', [TransactionsAddController::class, 'get_property_info']);
    Route::get('/agents/doc_management/transactions/update_county_select', [TransactionsAddController::class, 'update_county_select']);

    // ** FILL FIELDS

    // edit files
    Route::get('/agents/doc_management/transactions/edit_files/{document_id}', [TransactionsEditFilesController::class, 'file_view']);
    // get files
    Route::get('/agents/doc_management/transactions/get_edit_file_docs', [TransactionsEditFilesController::class, 'get_edit_file_docs']);

    // rotate document
    Route::post('/agents/doc_management/transactions/edit_files/rotate_document', [TransactionsEditFilesController::class, 'rotate_document']);

    // save field inputs
    Route::post('/agents/doc_management/transactions/edit_files/save_edit_system_inputs', [TransactionsEditFilesController::class, 'save_edit_system_inputs']);
    Route::post('/agents/doc_management/transactions/edit_files/save_edit_user_fields', [TransactionsEditFilesController::class, 'save_edit_user_fields']);

    // Export filled fields to pdf
    Route::post('/agents/doc_management/transactions/edit_files/convert_to_pdf', [TransactionsEditFilesController::class, 'convert_to_pdf']) -> name('convert_to_pdf');

    /********** Documents ********/

    // documents page
    Route::get('/documents', [DocumentsController::class, 'documents']);
    // get documents page
    Route::get('/documents/get_form_group_files', [DocumentsController::class, 'get_form_group_files']);



    // ****************** ESIGN **************** //


    // esign
    Route::get('/esign', [EsignController::class, 'esign']) -> name('esign');
    // esign after sending docs for signatures
    Route::get('/esign_show_sent', [EsignController::class, 'esign']);

    // save as draft
    Route::post('/esign/save_as_draft', [EsignController::class, 'save_as_draft']);

    // delete draft
    Route::post('/esign/delete_draft', [EsignController::class, 'delete_draft']);

    // restore draft
    Route::post('/esign/restore_draft', [EsignController::class, 'restore_draft']);

    // save as template
    Route::post('/esign/save_as_template', [EsignController::class, 'save_as_template']);

    // delete template
    Route::post('/esign/delete_template', [EsignController::class, 'delete_template']);

    // restore template
    Route::post('/esign/restore_template', [EsignController::class, 'restore_template']);

    // delete system template
    Route::post('/esign/delete_system_template', [EsignController::class, 'delete_system_template']);

    // restore system template
    Route::post('/esign/restore_system_template', [EsignController::class, 'restore_system_template']);

    // cancel envelope
    Route::post('/esign/cancel_envelope', [EsignController::class, 'cancel_envelope']);

    // resend envelope
    Route::post('/esign/resend_envelope', [EsignController::class, 'resend_envelope']);

    // get envelope
    Route::get('/esign/get_envelope', [EsignController::class, 'get_envelope']);

    // get esign dashboard tabs
    Route::get('/esign/get_drafts', [EsignController::class, 'get_drafts']);
    Route::get('/esign/get_deleted_drafts', [EsignController::class, 'get_deleted_drafts']);
    Route::get('/esign/get_in_process', [EsignController::class, 'get_in_process']);
    Route::get('/esign/get_completed', [EsignController::class, 'get_completed']);
    Route::get('/esign/get_templates', [EsignController::class, 'get_templates']);
    Route::get('/esign/get_deleted_templates', [EsignController::class, 'get_deleted_templates']);
    Route::get('/esign/get_system_templates', [EsignController::class, 'get_system_templates']);
    Route::get('/esign/get_deleted_system_templates', [EsignController::class, 'get_deleted_system_templates']);
    Route::get('/esign/get_canceled', [EsignController::class, 'get_canceled']);

    // add documents
    Route::get('/esign/esign_add_documents/{User_ID?}/{document_ids?}/{Agent_ID?}/{Listing_ID?}/{Contract_ID?}/{Referral_ID?}/{transaction_type?}', [EsignController::class, 'esign_add_documents']);
    Route::get('/esign/esign_add_documents_from_uploads/{document_id}/{is_template}', [EsignController::class, 'esign_add_documents']);

    Route::get('/esign/esign_add_template_documents/{template}', [EsignController::class, 'esign_add_documents']);

    // create envelope and send to add signers
    Route::post('/esign/esign_create_envelope', [EsignController::class, 'esign_create_envelope']);

    // add signers
    Route::get('/esign/esign_add_signers/{envelope_id}/{is_template?}/{template_id?}', [EsignController::class, 'esign_add_signers']);

    // add add signers to envelope
    Route::post('/esign/esign_add_signers_to_envelope', [EsignController::class, 'esign_add_signers_to_envelope']);

    // esign add fields
    Route::get('/esign/esign_add_fields/{envelope_id}/{is_template?}/{template_id?}', [EsignController::class, 'esign_add_fields']);
    Route::get('/esign/esign_add_fields_from_draft/{envelope_id}/{is_draft?}', [EsignController::class, 'esign_add_fields']);

    // send for signatures
    Route::post('/esign/esign_send_for_signatures', [EsignController::class, 'esign_send_for_signatures']);

    // upload docs for envelope
    Route::post('/esign/upload', [EsignController::class, 'upload']);

    // delete draft
    Route::post('/agents/doc_management/transactions/esign/delete_draft', [TransactionsDetailsController::class, 'delete_draft']);

    // restore draft
    Route::post('/agents/doc_management/transactions/esign/restore_draft', [TransactionsDetailsController::class, 'restore_draft']);

    // delete template
    Route::post('/agents/doc_management/transactions/esign/delete_template', [TransactionsDetailsController::class, 'delete_template']);

    // restore template
    Route::post('/agents/doc_management/transactions/esign/restore_template', [TransactionsDetailsController::class, 'restore_template']);

    /* // cancel envelope
    Route::post('/agents/doc_management/transactions/esign/cancel_envelope', [TransactionsDetailsController::class, 'cancel_envelope']);

    // resend envelope
    Route::post('/agents/doc_management/transactions/esign/resend_envelope', [TransactionsDetailsController::class, 'resend_envelope']); */

    // get esign dashboard tabs
    Route::get('/agents/doc_management/transactions/esign/get_drafts', [TransactionsDetailsController::class, 'get_drafts']);
    Route::get('/agents/doc_management/transactions/esign/get_deleted_drafts', [TransactionsDetailsController::class, 'get_deleted_drafts']);
    Route::get('/agents/doc_management/transactions/esign/get_in_process', [TransactionsDetailsController::class, 'get_in_process']);
    Route::get('/agents/doc_management/transactions/esign/get_completed', [TransactionsDetailsController::class, 'get_completed']);
    Route::get('/agents/doc_management/transactions/esign/get_canceled', [TransactionsDetailsController::class, 'get_canceled']);


});

// ****************** ESIGN **************** //

// callback url
Route::post('/esign_callback', [EsignController::class, 'esign_callback']);
Route::post('/oauth_callback', [EsignController::class, 'oauth_callback']);



