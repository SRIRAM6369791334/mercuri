<style>
    .topnav {
        position: sticky !important;
        top: 0 !important;
        z-index: 1020 !important;
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }
    .page-content,
    body[data-layout=horizontal] .page-content {
        margin-top: 0 !important;
        padding-top: 18px !important;
    }
.notification-settings-wrapper {
    display: flex;
    align-items: center;
}

.setting-menu {
    color: #374151;
    padding: 6px;
    display: flex;
    align-items: center;
}

.setting-menu:hover {
    color: #000;
}

.badge-count {
    position: absolute;
    top: 2px;
    right: 2px;
    background: red;
    color: #fff;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 50%;
}


.topnav .navbar {
    min-height: 56px;
    padding: 0 12px;
}
.navbar-brand-box  {
    padding: unset !important;
}

.navbar-brand-box img {
    height: 70px !important;
    width: 100px;
}
.topnav-menu .nav-link {
    font-size: 12.5px;
    font-weight: 600;
    color: #374151 !important;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.topnav-menu .nav-link i {
    width: 16px;
    height: 16px;
    stroke-width: 2;
}
.topnav-menu .nav-link:hover {
    background: #f1f5f9;
    border-radius: 6px;
}

.topnav-menu .nav-item.active .nav-link {
    background: #eef2ff;
    color: #2563eb !important;
}
.dropdown-menu {
    border-radius: 10px;
    padding: 6px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.dropdown-item {
    font-size: 12.5px;
    padding: 8px 12px;
    border-radius: 6px;
}

.dropdown-item:hover {
    background: #f8fafc;
}
.notification-bell-div {
    position: relative;
}

.notification-bell-div .mdi-bell {
    font-size: 18px;
}

.badge-count {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    padding: 1px 4px;
    border-radius: 999px;
}
.setting-menu i {
    font-size: 16px;
    margin-left: 8px;
    color: #475569;
}

.setting-menu:hover i {
    color: #2563eb;
}
.header-item {
    background: transparent !important;
    border: none !important;
    padding: 6px;
}

    </style>
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="navbar-brand-box">
                <a href="#" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/assets/images/logom.jpeg" alt="" height="70">
                        {{-- <span class="logo-txt">Mercury Storage Solutions</span>
                    </span> --}}
                    </span>
                    <span class="logo-lg">
                        <img src="/assets/images/logom.jpeg" alt="" height="70">

                        {{-- <span class="logo-txt">Mercury Storage Solutions</span>
                    </span> --}}
                    </span>
                </a>

                <a href="#" class="logo logo-light">
                    <span class="logo-sm">
                        {{-- <span class="logo-txt">Mercury Storage Solutions</span>
                    </span> --}}
                    <img src="/assets/images/logom.jpeg" alt="" height="70">
                    </span>
                    <span class="logo-lg">
                        <img src="/assets/images/logom.jpeg" alt="" height="70">

                        {{-- <span class="logo-txt">Mercury Storage Solutions</span>
                    </span> --}}
                    </span>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="/dashboard" id="topnav-dashboard"
                            role="button">
                            <i data-feather="home"></i><span data-key="t-dashboards">Dashboard</span>
                        </a>
                    </li>
                    @canany(['users-display', 'product-categories-display', 'products-display', 'customer-display', 'terms-and-condition-display'])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Master</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                @can('users-display')
                                    <a href="{{ url('/users') }}" class="dropdown-item" data-key="t-calendar">Users</a>
                                @endcan

                                @can('customer-display')
                                    <a href="{{ url('/customer') }}" class="dropdown-item" data-key="t-calendar">Customer</a>
                                @endcan

                                {{-- @can('product-categories-display')
                                    <a href="{{ url('/product-categories') }}" class="dropdown-item"
                                        data-key="t-calendar">Product Segment</a>
                                @endcan

                                @can('product-categories-display')
                                    <a href="{{ url('/product-sub-categories') }}" class="dropdown-item"
                                        data-key="t-calendar">Product group</a>
                                @endcan --}}

                                @can('products-display')
                                    <a href="{{ url('/products') }}" class="dropdown-item" data-key="t-calendar">Product
                                        Master</a>
                                         <a href="{{ url('/product-specifications') }}" class="dropdown-item" data-key="t-calendar">Product
                                        Specifications</a>
                                @endcan

                                @can('products-display')
                                    <a class="dropdown-item" href="{{ route('accessories.index') }}">Accessories Master</a>
                                @endcan
                                @can('terms-and-condition-display')
                                    <a href="/terms" class="dropdown-item" data-key="t-calendar">Terms and Conditions</a>
                                @endcan

                            </div>
                        </li>
                    @endcanany
                    @can('leads-display')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="/viewleads" id="topnav-dashboard"
                                role="button">
                                <i data-feather="home"></i><span data-key="t-dashboards">Leads</span>
                            </a>
                        </li>
                    @endcan
                    @can('design-display')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="/viewconverted" id="topnav-dashboard"
                                role="button">
                                <i data-feather="home"></i><span data-key="t-dashboards">Design</span>
                            </a>
                        </li>
                    @endcan

                    @can('quotation-display')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="/viewquota" id="topnav-dashboard"
                                role="button">
                                <i data-feather="home"></i><span data-key="t-dashboards">Quotation</span>
                            </a>
                        </li>
                    @endcan

                    @can('orf-display')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="/vieworfapp" id="topnav-dashboard"
                                role="button">
                                <i data-feather="home"></i><span data-key="t-dashboards">ORF</span>
                            </a>
                        </li>
                    @endcan

                    @can('reports-display')
                        <!-- <li class="nav-item dropdown">
                                                <a class="nav-link dropdown-toggle arrow-none" href="/viewreport" id="topnav-dashboard"
                                                    role="button">
                                                    <i data-feather="home"></i><span data-key="t-dashboards">Reports</span>
                                                </a>
                                            </li> -->
                    @endcan

                    @canany(['orf-approval', 'quotation-approval', 'discount-approval', 'terms-approval'])
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Approvals</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                @can("orf-approval")
                                    <a href="/vieworf" class="dropdown-item" data-key="t-calendar">ORF Approval</a>
                                @endcan

                                @canany(["quotation-approval", "discount-approval", "terms-approval"])
                                    <a href="/viewqutoapp" class="dropdown-item" data-key="t-calendar">Quotation Approval</a>
                                @endcanany
                            </div>
                        </li>
                    @endcanany

                    @can('purchase-order-display')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/order-po-tracking') }}" data-key="t-calendar">
                                <i data-feather="truck"></i><span>Supplier PO Tracking</span>
                            </a>
                        </li>
                    @endcan

                    @can('vendor-master-display')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Vendor Master</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">

                                @can('vendors-display')
                                    <a href="{{ url('/vendors') }}" class="dropdown-item" data-key="t-calendar">Vendors</a>
                                @endcan

                                @can('purchase-order-display')
                                    <a href="{{ url('/purchase-order-details') }}" class="dropdown-item"
                                        data-key="t-calendar">Purchase
                                        Order</a>
                                @endcan

                                @can('purchase-entry-display')
                                    <a href="{{ url('/purchase-entry') }}" class="dropdown-item" data-key="t-calendar">Purchase
                                        Entry</a>
                                @endcan

                                @can('product-stock-display')
                                    <a href="{{ url('/product-stocks') }}" class="dropdown-item" data-key="t-calendar">Product
                                        Stock</a>
                                @endcan

                            </div>
                        </li>
                    @endcan

                    @canany(['roles-and-permissions-display', 'roles-display', 'permissions-display'])
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle arrow-none" href="/roles" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Roles and Permissions</span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">

                                @can('roles-display')
                                    <a href="{{ url('/roles') }}" class="dropdown-item" data-key="t-calendar">Roles</a>
                                @endcan

                                @can('permissions-display')
                                    <a href="/permissions" class="dropdown-item">Permissions</a>
                                @endcan

                            </div>
                        </li>
                    @endcanany

                    {{-- @can('others-display') --}}
                    @if (Auth::user()?->role == 'Super Admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Others</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                <a href="{{ url('/expo') }}" class="dropdown-item" data-key="t-calendar">Expo</a>
                                <a href="{{ url('/lostleads') }}" class="dropdown-item" data-key="t-calendar">Lost Leads</a>
                                <a href="{{ url('/master/designation-department') }}" class="dropdown-item" data-key="t-calendar">Leads field master</a>
                                <a href="{{ url('/settings/mail') }}" class="dropdown-item" data-key="t-calendar">Mail Settings</a>
                                <a href="{{ url('/settings/assign') }}" class="dropdown-item" data-key="t-calendar">Assign Settings</a>
                                <a href="{{ url('/design-spec-master') }}" class="dropdown-item" data-key="t-calendar">Specifications Settings</a>
                                <a href="{{ url('/quotation-groups') }}" class="dropdown-item" data-key="t-calendar">Quotation Images</a>
                                 <a href="{{ url('/date-of-validity') }}" class="dropdown-item" data-key="t-calendar">Date of Validity</a>
                            </div>
                        </li>
                    @endif
                    @if(auth()->user()?->role === 'Design Engineer')

         <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="{{ route('design.engineer.list') }}" id="topnav-dashboard"
                                role="button">
                                <i data-feather="home"></i><span data-key="t-dashboards">Output Lists</span>
                            </a>
                        </li>
@endif

                    @if (Auth::user()?->role == 'HR')
                        {{-- @endcan --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Department</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                <a href="{{ url('/department') }}" class="dropdown-item" data-key="t-calendar">Add
                                    Department</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Employee</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                <a href="{{ url('/employeelist') }}" class="dropdown-item" data-key="t-calendar">Employee
                                    List</a>
                                <a href="{{ url('/employeeassetlist') }}" class="dropdown-item"
                                    data-key="t-calendar">Employee Asset</a>
                                <a href="{{ url('/employee-advance/all') }}" class="dropdown-item"
                                    data-key="t-calendar">Employee Advance</a>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button">
                                <i data-feather="grid"></i><span data-key="t-apps">Attendance</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-pages">
                                <a href="{{ url('/attendancelist') }}" class="dropdown-item"
                                    data-key="t-calendar">Attendance List</a>
                                <a href="{{ url('/addholiday') }}" class="dropdown-item" data-key="t-calendar">Add
                                    Holidays</a>
                            </div>
                        </li>

                    @endif

                </ul>
            </div>



            <div class="d-flex align-items-center gap-2">
                @php
                    use App\Models\Term;
                    use App\Models\Orf;
                    use App\Models\Lead;
                    use App\Models\QuotationProduct;
                    use App\Models\Freight;
                    use App\Models\PurchaseOrder;
                    use Illuminate\Support\Facades\Auth;
                    use Illuminate\Support\Facades\Schema;

                    $userRole = Auth::user()?->role;

                    // Fetch counts based on role
                    $termApprovalCount = 0;
                    $freightApprovalCount = 0;
                    $orfApprovalCount = 0;
                    $orfcsApprovalCount = 0;
                    $movequtoApprovalCount = 0;
                    $disApprovalCount = 0;
                    $stockCount = 0;
                    $pendingSirAdvanceCount = 0;
                    $pendingFinanceAdvanceCount = 0;
                    $rejectedSirAdvanceCount = 0;
                    $dispatchDueCount = 0;
                    $totalNotifications = 0;

                    $settingsDetails = App\Models\AdminSetting::first();
                    $hasPoTable = Schema::hasTable('purchase_order_tb');
                    $hasAdvCol = $hasPoTable && Schema::hasColumn('purchase_order_tb', 'advance_status');
                    $hasDispCol = $hasPoTable && Schema::hasColumn('purchase_order_tb', 'supplier_dispatch_date');

                    if ($hasDispCol) {
                        $sevenDaysLater = now()->addDays(7)->format('Y-m-d');
                        $dispatchDueCount = PurchaseOrder::whereNotNull('supplier_dispatch_date')
                            ->where('supplier_dispatch_date', '<=', $sevenDaysLater)
                            ->where(function($q) {
                                $q->whereNull('production_milestone')->orWhereNotIn('production_milestone', ['dispatched', 'delivered']);
                            })
                            ->distinct('po_no')
                            ->count('po_no');
                    }

                    if ($userRole === 'Service Coordinator' || $userRole === 'Cs') {
                        $orfcsApprovalCount = Orf::where('cs_status', 0)->count();
                        if ($hasAdvCol) {
                            $rejectedSirAdvanceCount = PurchaseOrder::where('advance_status', 'rejected_by_sir')->distinct('po_no')->count('po_no');
                        }
                        $totalNotifications = $orfcsApprovalCount + $rejectedSirAdvanceCount + $dispatchDueCount;
                    } elseif ($userRole === 'Design') {
                        // Design engineer role
                    } else {
                        $freightApprovalCount = Freight::where('freight_approve', 1)->count();
                        $termApprovalCount = Term::where('term_approve', 1)->count();
                        $orfApprovalCount = Orf::where('approval_status', 0)->count();
                        $movequtoApprovalCount = Lead::where('quota_proceed', 0)->count();
                        $disApprovalCount = QuotationProduct::where('dis_app', 1)->count();
                        $stockCount = App\Models\ProductStock::where("available_stock", '<=', $settingsDetails->product_stock_alert_count ?? 0)->count();

                        if ($hasAdvCol) {
                            $pendingSirAdvanceCount = PurchaseOrder::where('advance_status', 'pending_sir_approval')->distinct('po_no')->count('po_no');
                            $pendingFinanceAdvanceCount = PurchaseOrder::where('advance_status', 'approved_by_sir')->distinct('po_no')->count('po_no');
                        }

                        $totalNotifications = $termApprovalCount + $orfApprovalCount + $movequtoApprovalCount + $disApprovalCount + $freightApprovalCount + $stockCount + $pendingSirAdvanceCount + $pendingFinanceAdvanceCount + $dispatchDueCount;
                    }

                    $userUnreadNotifications = Auth::check() ? Auth::user()->unreadNotifications()->take(5)->get() : collect();
                    $userUnreadCount = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;
                    $grandTotalBellCount = $totalNotifications + $userUnreadCount;
                @endphp

                <!-- Notification Bell -->
                <div class="dropdown notification-bell-div">
                    <a class="nav-link dropdown-toggle arrow-none position-relative text-dark d-flex align-items-center"
                       href="#"
                       id="topbarNotificationBell"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false"
                       style="padding: 8px 10px;">
                        <i class="mdi mdi-bell fs-5"></i>
                        @if ($grandTotalBellCount > 0)
                            <span class="badge-count">{{ $grandTotalBellCount }}</span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="topbarNotificationBell" style="min-width: 280px; max-width: 340px;">
                        <div class="px-3 py-2 border-bottom fw-bold text-dark small d-flex justify-content-between align-items-center bg-light">
                            <span><i class="fas fa-bell me-1 text-primary"></i> Notifications</span>
                            <div class="d-flex align-items-center gap-1">
                                @if ($grandTotalBellCount > 0)
                                    <span class="badge bg-danger">{{ $grandTotalBellCount }} New</span>
                                @endif
                                @if ($userUnreadCount > 0)
                                    <button type="button" class="btn btn-link btn-xs p-0 text-primary small text-decoration-none" onclick="markAllNotificationsRead()" title="Mark all read" style="font-size: 11px;">
                                        Mark read
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($userUnreadNotifications->isNotEmpty())
                            <div class="border-bottom pb-1 mb-1">
                                @foreach ($userUnreadNotifications as $notif)
                                    @php
                                        $nData = $notif->data ?? [];
                                        $nTitle = $nData['title'] ?? 'PO Alert';
                                        $nMsg = $nData['message'] ?? 'Notification';
                                        $nUrl = $nData['url'] ?? route('order-po-tracking');
                                    @endphp
                                    <a href="javascript:void(0)" onclick="markNotificationRead('{{ $notif->id }}', '{{ $nUrl }}')" class="dropdown-item py-2 px-3 border-bottom text-wrap" style="background-color: #f0f9ff; font-size: 11.5px;">
                                        <div class="fw-bold text-primary mb-1 d-flex justify-content-between">
                                            <span><i class="fas fa-bell text-warning me-1"></i>{{ $nTitle }}</span>
                                            <small class="text-muted fw-normal">{{ $notif->created_at ? $notif->created_at->diffForHumans(null, true, true) : '' }}</small>
                                        </div>
                                        <div class="text-dark small lh-sm">{{ $nMsg }}</div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if ($totalNotifications === 0 && $userUnreadCount === 0)
                            <div class="px-3 py-3 text-center text-muted small">
                                <i class="fas fa-check-circle text-success mb-1 d-block fs-5"></i>
                                All caught up! No pending items.
                            </div>
                        @else
                            @if ($dispatchDueCount > 0)
                                <a href="{{ route('order-po-tracking') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 text-warning">
                                    <span><i class="fas fa-truck text-warning me-2"></i>Dispatch Due (Within 1 Wk)</span>
                                    <span class="badge bg-warning text-dark">{{ $dispatchDueCount }}</span>
                                </a>
                            @endif

                            @if (($userRole === 'Service Coordinator' || $userRole === 'Cs') && $orfcsApprovalCount > 0)
                                <a href="{{ url('/vieworf') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-clipboard-check text-info me-2"></i>ORF CS Approval</span>
                                    <span class="badge bg-danger">{{ $orfcsApprovalCount }}</span>
                                </a>
                            @endif

                            @if (($userRole === 'Service Coordinator' || $userRole === 'Cs') && $rejectedSirAdvanceCount > 0)
                                <a href="{{ route('order-po-tracking', ['status' => 'rejected_sir']) }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 text-danger">
                                    <span><i class="fas fa-times-circle me-2"></i>Advance Rejected by Sir</span>
                                    <span class="badge bg-danger">{{ $rejectedSirAdvanceCount }}</span>
                                </a>
                            @endif

                            @if ($pendingSirAdvanceCount > 0)
                                <a href="{{ route('order-po-tracking', ['status' => 'pending_sir']) }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 text-danger fw-semibold">
                                    <span><i class="fas fa-user-shield me-2"></i>Sir Advance Approvals</span>
                                    <span class="badge bg-danger">{{ $pendingSirAdvanceCount }}</span>
                                </a>
                            @endif

                            @if ($pendingFinanceAdvanceCount > 0)
                                <a href="{{ route('order-po-tracking', ['status' => 'pending_finance']) }}" class="dropdown-item d-flex justify-content-between align-items-center py-2 text-primary fw-semibold">
                                    <span><i class="fas fa-money-check-alt me-2"></i>Finance UTR Release</span>
                                    <span class="badge bg-primary">{{ $pendingFinanceAdvanceCount }}</span>
                                </a>
                            @endif

                            @if ($userRole === 'Super Admin' && $termApprovalCount > 0)
                                <a href="{{ url('/terms') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-file-contract text-secondary me-2"></i>Terms Approval</span>
                                    <span class="badge bg-danger">{{ $termApprovalCount }}</span>
                                </a>
                            @endif

                            @if ($userRole === 'Super Admin' && $orfApprovalCount > 0)
                                <a href="{{ url('/vieworf') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-tasks text-warning me-2"></i>ORF Approval</span>
                                    <span class="badge bg-danger">{{ $orfApprovalCount }}</span>
                                </a>
                            @endif

                            @if ($userRole === 'Super Admin' && $movequtoApprovalCount > 0)
                                <a href="{{ url('/viewmovequto') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-share-square text-success me-2"></i>Move Quotation</span>
                                    <span class="badge bg-danger">{{ $movequtoApprovalCount }}</span>
                                </a>
                            @endif

                            @if ($userRole === 'Super Admin' && $disApprovalCount > 0)
                                <a href="{{ url('/viewqutoapp') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-percent text-info me-2"></i>Discount Approval</span>
                                    <span class="badge bg-danger">{{ $disApprovalCount }}</span>
                                </a>
                            @endif

                            @if ($userRole === 'Super Admin' && $freightApprovalCount > 0)
                                <a href="{{ url('/viewqutoapp') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-truck text-dark me-2"></i>Freight Approval</span>
                                    <span class="badge bg-danger">{{ $freightApprovalCount }}</span>
                                </a>
                            @endif

                            @if (($userRole === 'Super Admin' || $userRole === 'Purchase Manager') && $stockCount > 0)
                                <a href="{{ url('/product-stocks') }}" class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                    <span><i class="fas fa-boxes text-danger me-2"></i>Low Product Stock</span>
                                    <span class="badge bg-danger">{{ $stockCount }}</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Settings Icon -->
                <a href="{{ url('/settings') }}" class="setting-menu" title="System Settings">
                    <i class="fas fa-cog fs-5"></i>
                </a>
            </div>
        </nav>
    </div>
</div>

<script>
function markNotificationRead(id, redirectUrl) {
    fetch('/notifications/' + id + '/mark-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        }
    }).finally(() => {
        if (redirectUrl) window.location.href = redirectUrl;
    });
}
function markAllNotificationsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        }
    }).then(() => {
        window.location.reload();
    });
}
</script>
