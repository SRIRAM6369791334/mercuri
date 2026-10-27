<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title" data-key="t-menu">Mercury Storage Solutions</li> --}}

                <li>
                    <a href="/dashboard">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="/viewleads" >
                        <i data-feather="users"></i>
                        <span data-key="t-authentication">Leads</span>
                    </a>
                </li>

                <li class="">
                    <a href="javascript: void(0);" class="has-arrow" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Master</span>
                    </a>
                    <ul class="sub-menu collapse collapse-horizontal" aria-expanded="false" id="collapseWidthExample">
                        <li>
                            <a href="{{ url('/setup/fields') }}">
                                <span data-key="t-calendar">Field
                                    Customization</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/users') }}">
                                <span data-key="t-calendar">Users</span>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>

            {{-- <div class="card sidebar-alert border-0 text-center mx-4 mb-0 mt-5">
                <div class="card-body">
                    <img src="/images/giftbox.png" alt="">
                    <div class="mt-4">
                        <h5 class="alertcard-title font-size-16">{% trans %}Unlimited Access{% endtrans %}</h5>
                        <p class="font-size-13">{% trans %}Upgrade your plan from a Free trial, to select ‘Business Plan’{% endtrans %}</p>
                        <a href="#!" class="btn btn-primary mt-2">{% trans %}Upgrade Now{% endtrans %}</a>
                    </div>
                </div>
            </div> --}}
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
