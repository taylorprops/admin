<li class="nav-item mx-2">
    <a href="/dashboard_admin" class="nav-link"> Dashboard</a>
</li>

<li class="nav-item dropdown mx-2">

    <a class="nav-link dropdown-toggle" href="javascript: void(0)" id="transactions_dropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        Transactions
    </a>
    <ul class="dropdown-menu" aria-labelledby="transactions_dropdown">

        <li><a href="/agents/doc_management/transactions" class="dropdown-item">Transactions</a></li>

        <li class="nav-item dropdown">
            <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="transactions_sub_dropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Add
            </a>
            <ul class="dropdown-menu" aria-labelledby="transactions_sub_dropdown">
                <li>
                    <a href="/agents/doc_management/transactions/add/listing" class="dropdown-item">Add Listing</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/contract" class="dropdown-item">Add Contract</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/referral" class="dropdown-item">Add Referral</a>
                </li>
            </ul>
        </li>

        <li>
            <a href="/documents" class="dropdown-item"> Documents</a>
        </li>

        <li class="nav-item dropdown">
            <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="management_dropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Management
            </a>
            <ul class="dropdown-menu" aria-labelledby="management_dropdown">
                <li class="nav-item dropdown">
                    <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="resources_dropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Resources
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="resources_dropdown">
                        @if(session('super_user') == true)
                        <li><a href="/doc_management/resources/resources" class="dropdown-item"> Site Resources</a></li>
                        <li><a href="/doc_management/resources/common_fields" class="dropdown-item"> Common Fields</a></li>
                        @endif
                        <li><a href="/admin/resources/resources_admin" class="dropdown-item"> Admin Resources</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/doc_management/create/upload/files" class="dropdown-item"> Files</a>
                </li>
                <li class="nav-item">
                    <a href="/doc_management/checklists" class="dropdown-item"> Checklists</a>
                </li>
            </ul>

        </li>


    </ul>

</li>



<li class="nav-item dropdown mx-2">

    <a class="nav-link dropdown-toggle" href="javascript: void(0)" id="admin_dropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        Admin
    </a>
    <ul class="dropdown-menu" aria-labelledby="admin_dropdown">

        <li>
            <a href="/doc_management/document_review" class="dropdown-item">Review Documents</a>
        </li>

        <li>
            <a href="/doc_management/commission" class="dropdown-item">Breakdowns/Checks</a>
        </li>

        <li class="nav-item dropdown">
            <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="earnest_sub_dropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Earnest
            </a>
            <ul class="dropdown-menu" aria-labelledby="earnest_sub_dropdown">
                <li>
                    <a href="/doc_management/active_earnest" class="dropdown-item">Active Earnest Deposits</a>
                </li>
                <li>
                    <a href="/doc_management/balance_earnest" class="dropdown-item">Balance Earnest</a>
                </li>
            </ul>
        </li>

    </ul>

</li>


<li class="nav-item mx-2">
    <a href="/esign" class="nav-link"> E-Sign</a>
</li>





<li class="nav-item dropdown mx-2">

    <a class="nav-link dropdown-toggle" href="javascript: void(0)" id="settings_dropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        Settings
    </a>
    <ul class="dropdown-menu" aria-labelledby="settings_dropdown">

        @if(stristr(config('global_db.permission_edit_employees'), auth() -> user() -> email))
        <li>
            <a href="/employees" class="dropdown-item"> Employees</a>
        </li>
        @endif

        @if(stristr(config('global_db.permission_edit_permissions'), auth() -> user() -> email) || auth() -> user() -> super_user == 'yes')
        <li>
            <a href="/permissions/permissions" class="dropdown-item">Permissions</a>
        </li>
        @endif

        @if(stristr(config('global_db.permission_edit_notifications'), auth() -> user() -> email))
        <li class="nav-item">
            <a href="/doc_management/notifications" class="dropdown-item"> Notification Settings</a>
        </li>
        @endif

    </ul>

</li>
