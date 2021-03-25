<ul>
    <li class="mt-4">
        <a href="/dashboard_agent">
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
                    <a href="/agents/doc_management/transactions/add/contract">Add Contract</a>
                </li>
                <li>
                    <a href="/agents/doc_management/transactions/add/referral">Add Referral</a>
                </li>

            </ul>
        </div>
    </li>

    @if(auth() -> user() -> group == 'agent')
        <li>
            <a href="/esign">
                <i class="fa fa-signature"></i>
                E-Sign
            </a>
        </li>

        <li>
            <a href="/contacts">
                <i class="fa fa-users"></i>
                Contacts
            </a>
        </li>
    @endif

    <li>
        <a href="/documents">
            <i class="fa fa-book"></i>
            Documents
        </a>
    </li>
