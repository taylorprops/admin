<ul>
    <li class="">
        <a href="/dashboard_admin">
            <i class="fa fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="sidebar-dropdown">
        <a href="#">
            <i class="fa fa-sign"></i>
            <span>Transactions</span>
            <span class="badge badge-pill badge-danger">3</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="/agents/doc_management/transactions">Transactions</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/listing">Add Listing</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/contract">Add Contract</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/referral">Add Referral</a>
                </li>
            </ul>
        </div>
    </li>

    <li>
        <a href="/documents">
            <i class="fal fa-file"></i>
            <span>Documents</span>
        </a>
    </li>
    <li class="sidebar-dropdown">
        <a href="#">
            <i class="far fa-gem"></i>
            <span>Management</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="#">Resources</a>
                    <ul>
                        @if(session('super_user') == true)
                        <li><a href="/doc_management/resources/resources"> Site Resources</a></li>
                        <li><a href="/doc_management/resources/common_fields"> Common Fields</a></li>
                        @endif
                        <li><a href="/admin/resources/resources_admin"> Admin Resources</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Panels</a>
                </li>
                <li>
                    <a href="#">Tables</a>
                </li>
                <li>
                    <a href="#">Icons</a>
                </li>
                <li>
                    <a href="#">Forms</a>
                </li>
            </ul>
        </div>
    </li>
    <li class="sidebar-dropdown">
        <a href="#">
            <i class="fa fa-chart-line"></i>
            <span>Charts</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="#">Pie chart</a>
                </li>
                <li>
                    <a href="#">Line chart</a>
                </li>
                <li>
                    <a href="#">Bar chart</a>
                </li>
                <li>
                    <a href="#">Histogram</a>
                </li>
            </ul>
        </div>
    </li>
    <li class="sidebar-dropdown">
        <a href="#">
            <i class="fa fa-globe"></i>
            <span>Maps</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="#">Google maps</a>
                </li>
                <li>
                    <a href="#">Open street map</a>
                </li>
            </ul>
        </div>
    </li>
    <li class="header-menu">
        <span>Extra</span>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-book"></i>
            <span>Documentation</span>
            <span class="badge badge-pill badge-primary">Beta</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-calendar"></i>
            <span>Calendar</span>
        </a>
    </li>
    <li>
        <a href="#">
            <i class="fa fa-folder"></i>
            <span>Examples</span>
        </a>
    </li>
</ul>











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
