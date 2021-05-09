<ul>
    <li class="mt-2">
        <a href="/dashboard">
            <i class="fa fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li><hr class="my-1"></li>

    <li class="header-menu">
        <span>Doc Managment</span>
    </li>

    <li class="sidebar-dropdown">
        <a href="javascript:void(0)">
            <i class="fa fa-sign"></i>
            <span>Transactions</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="/agents/doc_management/transactions">View All Transactions</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/listing">Add Listing</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/contract">Add Contract/Lease</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/referral">Add Referral</a>
                </li>

            </ul>
        </div>
    </li>

    <li class="sidebar-dropdown">
        <a href="javascript:void(0)">
            <i class="far fa-tasks-alt"></i>
            <span>Resources</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                @if(stristr(config('notifications.permission_edit_association_files'), auth() -> user() -> email))
                <li>
                    <a href="/doc_management/create/upload/files"> Files</a>
                </li>
                <li>
                    <a href="/doc_management/checklists"> Checklists</a>
                </li>
                @endif
                <li>
                    <a href="javascript:void(0)">Resources</a>
                    <ul>
                        @if(session('super_user') == true)
                        <li><a href="/doc_management/resources/resources"> Site Resources</a></li>
                        <li><a href="/doc_management/resources/common_fields"> Common Fields</a></li>
                        @endif
                        <li><a href="/admin/resources/resources_admin"> Admin Resources</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </li>

    <li>
        <a href="/documents">
            <i class="far fa-book"></i>
            <span>Documents</span>
        </a>
    </li>

    <li><hr class="my-1"></li>

    <li class="header-menu">
        <span>Admin</span>
    </li>

    <li>
        <a href="/doc_management/document_review">
            <i class="far fa-file"></i>
            Review Documents
        </a>
    </li>

    <li>
        <a href="/doc_management/commission">
            <i class="far fa-money-check-alt"></i>
            Breakdowns/Checks
        </a>
    </li>

    <li class="sidebar-dropdown">
        <a href="javascript:void(0)">
            <i class="fa fa-money-bill-wave"></i>
            <span>Earnest</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                <li>
                    <a href="/doc_management/active_earnest">Active Earnest Deposits</a>
                </li>
                <li>
                    <a href="/doc_management/balance_earnest">Balance Earnest</a>
                </li>
            </ul>
        </div>
    </li>

    <li>
        <a href="/doc_management/compliance/missing_transactions">
            <i class="far fa-exclamation-circle"></i>
            Missing Transactions
        </a>
    </li>


    @if(session('super_user') == true)
        <li>
            <a href="/bug_reports">
                <i class="far fa-bug"></i>
                Bug Reports
            </a>
        </li>
    @endif

    <li><hr class="my-2"></li>


    <li>
        <a href="/esign">
            <i class="fa fa-signature"></i>
            E-Sign
        </a>
    </li>

    @if(auth() -> user() -> super_user == 'yes' ||
        stristr(config('notifications.permission_edit_permissions'), auth() -> user() -> email) ||
        stristr(config('notifications.permission_edit_notifications'), auth() -> user() -> email))

    <li><hr class="my-2"></li>

    @if(stristr(config('notifications.permission_edit_employees'), auth() -> user() -> email))
        <li>
            <a href="/employees">
                <i class="fa fa-users"></i>
                Employees</a>
        </li>
    @endif

    <li class="sidebar-dropdown">
        <a href="javascript:void(0)">
            <i class="fa fa-globe"></i>
            <span>Settings</span>
        </a>
        <div class="sidebar-submenu">
            <ul>
                @if(auth() -> user() -> super_user == 'yes' ||
                    stristr(config('notifications.permission_edit_permissions'), auth() -> user() -> email))
                <li>
                    <a href="/permissions/permissions">Permissions</a>
                </li>
                @endif

                @if(stristr(config('notifications.permission_edit_notifications'), auth() -> user() -> email))
                <li class="nav-item">
                    <a href="/doc_management/notifications"> Notification Settings</a>
                </li>
                @endif
            </ul>
        </div>
    </li>

    @endif

    @if(auth() -> user() -> group == 'admin')
    <li>
        <a href="/users">
            <i class="fa fa-users"></i>
            Website Users</a>
    </li>
    @endif


</ul>
