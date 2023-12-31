<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('ui-kit/images/logo.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Don Bosco</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('home.dashboard') }}">
                <div class="parent-icon"><i class='bx bx-home-circle'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        @role(['admin'])
        <li>
            <a href="{{ route('project.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-calendar-alt'></i>
                </div>
                <div class="menu-title">Manage Projects</div>
            </a>
        </li>
        <li>
            <a href="{{ route('account.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-user-plus'></i>
                </div>
                <div class="menu-title">Manage Accounts</div>
            </a>
        </li>
        <li>
            <a href="{{ route('treasurer.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-shield'></i>
                </div>
                <div class="menu-title">Treasurers</div>
            </a>
        </li>
        <li>
            <a href="javascript: void(0);" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-user-pin'></i>
                </div>
                <div class="menu-title">Manage Groups</div>
            </a>
            <ul>
                <li> <a href="{{ route('group.manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Main Groups</a></li>
                <li> <a href="{{ route('sub-group.manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Sub-Groups</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ route('expense.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bxs-credit-card'></i>
                </div>
                <div class="menu-title">Expenses</div>
            </a>
        </li>
        <li>
            <a href="{{ route('mpesa.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-search'></i>
                </div>
                <div class="menu-title">M-pesa Reconciliation</div>
            </a>
        </li>
        <li>
            <a href="{{ route('home.send-sms-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-message-detail'></i>
                </div>
                <div class="menu-title">Send SMS's</div>
            </a>
        </li>
        @endrole
        @role(['admin'])
        <li>
            <a href="javascript: void(0);">
                <div class="parent-icon">
                    <i class='bx bx-user'></i>
                </div>
                <div class="menu-title">Manage Users</div>
            </a>
        </li>
        @endrole
        @role(['admin', 'treasurer'])
        <li>
            <a href="{{ route('donor.manage-page') }}">
                <div class="parent-icon">
                    <i class='bx bx-donate-heart'></i>
                </div>
                <div class="menu-title">Donors</div>
            </a>
        </li>
        <li>
            <a href="javascript: void(0);" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-bar-chart-alt-2'></i>
                </div>
                <div class="menu-title">Reports</div>
            </a>
            <ul>
                <li> <a href="{{ route('report.account-donations-manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Account Donations</a></li>
                <li> <a href="{{ route('report.project-donations-manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Project Donations </a></li>
                <li> <a href="{{ route('report.pledge-donations-manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Pledge Donations </a></li>
                <li> <a href="{{ route('report.all-donations-manage-page') }}"><i class="bx bx-right-arrow-alt"></i>All Donations</a></li>
                <li> <a href="{{ route('report.fund-distribution-manage-page') }}"><i class="bx bx-right-arrow-alt"></i>Fund Distribution</a></li>
            </ul>
        </li>
        @endrole
    </ul>
</div>
