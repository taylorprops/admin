<?php
use Illuminate\Support\Facades\Route;

// esign
Route::get('/esign', 'Esign\EsignController@esign') -> name('esign');
// esign after sending docs for signatures
Route::get('/esign_show_sent', 'Esign\EsignController@esign');

// callback url
Route::post('/esign_callback', 'Esign\EsignController@esign_callback');
Route::post('/oauth_callback', 'Esign\EsignController@oauth_callback');

// save as draft
Route::post('/esign/save_as_draft', 'Esign\EsignController@save_as_draft');

// delete draft
Route::post('/esign/delete_draft', 'Esign\EsignController@delete_draft');

// restore draft
Route::post('/esign/restore_draft', 'Esign\EsignController@restore_draft');

// save as template
Route::post('/esign/save_as_template', 'Esign\EsignController@save_as_template');

// delete template
Route::post('/esign/delete_template', 'Esign\EsignController@delete_template');

// restore template
Route::post('/esign/restore_template', 'Esign\EsignController@restore_template');

// delete system template
Route::post('/esign/delete_system_template', 'Esign\EsignController@delete_system_template');

// restore system template
Route::post('/esign/restore_system_template', 'Esign\EsignController@restore_system_template');

// cancel envelope
Route::post('/esign/cancel_envelope', 'Esign\EsignController@cancel_envelope');

// resend envelope
Route::post('/esign/resend_envelope', 'Esign\EsignController@resend_envelope');

// get esign dashboard tabs
Route::get('/esign/get_drafts', 'Esign\EsignController@get_drafts');
Route::get('/esign/get_deleted_drafts', 'Esign\EsignController@get_deleted_drafts');
Route::get('/esign/get_in_process', 'Esign\EsignController@get_in_process');
Route::get('/esign/get_completed', 'Esign\EsignController@get_completed');
Route::get('/esign/get_templates', 'Esign\EsignController@get_templates');
Route::get('/esign/get_deleted_templates', 'Esign\EsignController@get_deleted_templates');
Route::get('/esign/get_system_templates', 'Esign\EsignController@get_system_templates');
Route::get('/esign/get_deleted_system_templates', 'Esign\EsignController@get_deleted_system_templates');
Route::get('/esign/get_cancelled', 'Esign\EsignController@get_cancelled');




// add documents
Route::get('/esign/esign_add_documents/{User_ID?}/{document_ids?}/{Agent_ID?}/{Listing_ID?}/{Contract_ID?}/{Referral_ID?}/{transaction_type?}', 'Esign\EsignController@esign_add_documents');
Route::get('/esign/esign_add_documents_from_uploads/{document_id}/{is_template}', 'Esign\EsignController@esign_add_documents');

Route::get('/esign/esign_add_template_documents/{template}', 'Esign\EsignController@esign_add_documents');

// create envelope and send to add signers
Route::post('/esign/esign_create_envelope', 'Esign\EsignController@esign_create_envelope');

// add signers
Route::get('/esign/esign_add_signers/{envelope_id}/{is_template?}/{template_id?}', 'Esign\EsignController@esign_add_signers');

// add add signers to envelope
Route::post('/esign/esign_add_signers_to_envelope', 'Esign\EsignController@esign_add_signers_to_envelope');

// esign add fields
Route::get('/esign/esign_add_fields/{envelope_id}/{is_template?}/{template_id?}', 'Esign\EsignController@esign_add_fields');
Route::get('/esign/esign_add_fields_from_draft/{envelope_id}/{is_draft?}', 'Esign\EsignController@esign_add_fields');

// send for signatures
Route::post('/esign/esign_send_for_signatures', 'Esign\EsignController@esign_send_for_signatures');

// upload docs for envelope
Route::post('/esign/upload', 'Esign\EsignController@upload');


// delete draft
Route::post('/agents/doc_management/transactions/esign/delete_draft', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@delete_draft');

// restore draft
Route::post('/agents/doc_management/transactions/esign/restore_draft', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@restore_draft');

// delete template
Route::post('/agents/doc_management/transactions/esign/delete_template', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@delete_template');

// restore template
Route::post('/agents/doc_management/transactions/esign/restore_template', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@restore_template');

// cancel envelope
Route::post('/agents/doc_management/transactions/esign/cancel_envelope', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@cancel_envelope');

// resend envelope
Route::post('/agents/doc_management/transactions/esign/resend_envelope', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@resend_envelope');

// get esign dashboard tabs
Route::get('/agents/doc_management/transactions/esign/get_drafts', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_drafts');
Route::get('/agents/doc_management/transactions/esign/get_deleted_drafts', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_deleted_drafts');
Route::get('/agents/doc_management/transactions/esign/get_in_process', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_in_process');
Route::get('/agents/doc_management/transactions/esign/get_completed', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_completed');
Route::get('/agents/doc_management/transactions/esign/get_cancelled', 'Agents\DocManagement\Transactions\Details\TransactionsDetailsController@get_cancelled');
