import $ from 'jquery';
window.$ = window.jQuery = $;
const axios = require('axios');
import 'jquery-ui/ui/widgets/datepicker.js';
require('dm-file-uploader');

import * as FilePond from "filepond";
window.FilePond = FilePond;

import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileEncode from 'filepond-plugin-file-encode';
// import FilePondPluginImageCrop from "filepond-plugin-image-crop";
// import FilePondPluginImageTransform from "filepond-plugin-image-transform";
// import FilePondPluginImageEdit from "filepond-plugin-image-edit";
// import FilePondPluginImageResize from "filepond-plugin-image-resize";

FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.registerPlugin(FilePondPluginFileEncode);
// FilePond.registerPlugin(FilePondPluginImageCrop);
// FilePond.registerPlugin(FilePondPluginImageTransform);
// FilePond.registerPlugin(FilePondPluginImageEdit);
// FilePond.registerPlugin(FilePondPluginImageResize);

import Cropper from 'cropperjs';
window.Cropper = Cropper;

require('./bootstrap');

require('./global.js');
require('./form_elements.js');
require('./nav/nav.js');
require('./nav/search.js');

// dashboard
require('./dashboard/dashboard_admin.js');
require('./dashboard/dashboard_agent.js');

// Document Management

require('./doc_management/resources/resources.js');
require('./doc_management/resources/common_fields.js');
require('./admin/resources/resources.js');
require('./admin/permissions/permissions.js');
require('./doc_management/notifications/notifications.js');

require('./doc_management/create/add_fields.js');
require('./doc_management/create/files.js');
//require('./doc_management/fill/fill_fields.js');
require('./doc_management/checklists/checklists.js');

// Agents
require('./agents/doc_management/transactions/add/transaction_add_details.js');
require('./agents/doc_management/transactions/add/transaction_required_details.js');
require('./agents/doc_management/transactions/add/transaction_add.js');
require('./agents/doc_management/transactions/details/transaction_details.js');
require('./agents/doc_management/transactions/transactions.js');
// details tabs
require('./agents/doc_management/transactions/details/details_tabs/details.js');
require('./agents/doc_management/transactions/details/details_tabs/members.js');
require('./agents/doc_management/transactions/details/details_tabs/documents.js');
require('./agents/doc_management/transactions/details/details_tabs/esign.js');
require('./agents/doc_management/transactions/details/details_tabs/checklist.js');
require('./agents/doc_management/transactions/details/details_tabs/contracts.js');
require('./agents/doc_management/transactions/details/details_tabs/commission.js');
require('./agents/doc_management/transactions/details/details_tabs/commission_other.js');
require('./agents/doc_management/transactions/details/details_tabs/agent_commission.js');
require('./agents/doc_management/transactions/details/details_tabs/earnest.js');
require('./agents/doc_management/transactions/upload/upload.js');

require('./agents/doc_management/transactions/shared/checklist_review.js');

// edit files
require('./agents/doc_management/transactions/edit_files/edit_files.js');

// esign
require('./esign/esign.js');
require('./esign/esign_add_signers.js');
require('./esign/esign_add_documents.js');
require('./esign/esign_add_fields.js');


// review documents
require('./doc_management/review/review.js');

// CRM
require('./CRM/contacts.js');

// commission
require('./doc_management/commission/commission_breakdowns.js');

// earnest
require('./doc_management/earnest/balance_earnest.js');
require('./doc_management/earnest/active_earnest.js');

// documents
require('./agents/doc_management/documents/documents.js');

// employees
require('./employees/employees.js');





