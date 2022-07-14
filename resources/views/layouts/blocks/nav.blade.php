<!-- START LEFT SIDEBAR NAV-->
<aside id="left-sidebar-nav" class="nav-expanded nav-lock nav-collapsible" v-if="typeof pageMode !== 'undefined' && pageMode === 'default'">
    <div class="brand-sidebar">
        <h1 class="logo-wrapper">
            <a href="{{ route('home') }}" class="brand-logo darken-1">
                @if (empty($partner) || empty($partner->logo))
                    <img src="{{ cdn('images/icon-only.png') }}" alt="materialize logo">
                @else
                    <img src="{{ $partner->logo }}" alt="materialize logo" style="height: 30px;">
                @endif
            
                <span class="logo-text hide-on-med-and-down">
                    {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : 'Hub' }}
                    @if (empty($appUiSettings['product_name']) && !empty($business))
                        <small>{{ title_case($business->plan['data']['name']) }}</small>
                    @endif
                </span>
            </a>
            <a href="#" class="navbar-toggler">
                <i class="material-icons">radio_button_checked</i>
            </a>
        </h1>
    </div>
    <ul id="slide-out" class="side-nav fixed leftside-navigation">
        <li class="no-padding" id="nav-item-dashboard">
            <ul class="collapsible" data-collapsible="accordion">
                <li class="bold {{ !empty($currentPage) && $currentPage === 'home' ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="active">
                        <i class="material-icons">dashboard</i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            </ul>
        </li>
        @if (empty($viewMode) || $viewMode === 'business')
            <li class="li-hover" id="nav-item-business">
                <p class="ultra-small margin more-text">{{ !empty($business) ? str_limit(title_case($business->name), 20) : '' }}</p>
            </li>
            @if (!isset($showUiModalAccessMenu) || !empty($showUiModalAccessMenu))
                <li class="bold {{ !empty($currentPage) && $currentPage === 'subscription' ? 'active' : '' }}"
                    id="nav-item-business-subscription" v-if="!UiUsesGrants">
                    <a href="{{ route('subscription') }}">
                        <i class="material-icons">history</i>
                        <span class="nav-text">Subscription</span>
                    </a>
                </li>
            @endif
            @if (!empty($isOnPremiumPlan))
                <!--<li class="li-hover">
                    <p class="ultra-small margin more-text">Premium Access</p>
                </li>
                <li class="bold {{ !empty($currentPage) && $currentPage === 'users' ? 'active' : '' }}" id="nav-item-premium-users">
                    <a href="">
                        <i class="material-icons">supervisor_account</i>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                <li class="bold {{ !empty($currentPage) && $currentPage === 'invites' ? 'active' : '' }}" id="nav-item-premium-invites">
                    <a href="">
                        <i class="material-icons">move_to_inbox</i>
                        <span class="nav-text">Invites</span>
                    </a>
                </li>-->
            @endif
            <li class="no-padding {{ !empty($currentPage) && $currentPage === 'crm' ? 'active' : '' }}" v-if="enabledUis.indexOf('customers') !== -1">
                <ul class="collapsible" data-collapsible="accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect {{ !empty($currentPage) && $currentPage === 'crm' ? 'active' : '' }}" id="nav-item-apps-crm">
                            <i class="material-icons">face</i>
                            <span class="nav-text">Customers</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'customers' ? 'active' : '' }}" id="nav-item-apps-crm-customers">
                                    <a href="{{ route('apps.crm.customers') }}">
                                        <i class="material-icons">wc</i>
                                        <span>Customers</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'contact-fields' ? 'active' : '' }}" id="nav-item-apps-crm-custom-fields">
                                    <a href="{{ route('apps.crm.custom-fields') }}">
                                        <i class="material-icons">widgets</i>
                                        <span>Custom Fields</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'groups' ? 'active' : '' }}" id="nav-item-apps-crm-groups">
                                    <a href="{{ route('apps.crm.groups') }}">
                                        <i class="material-icons">compare</i>
                                        <span>Groups</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="no-padding {{ !empty($currentPage) && $currentPage === 'ecommerce' ? 'active' : '' }}" v-if="enabledUis.indexOf('ecommerce') !== -1">
                <ul class="collapsible" data-collapsible="accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect {{ !empty($currentPage) && $currentPage === 'ecommerce' ? 'active' : '' }}" id="nav-item-ecommerce">
                            <i class="material-icons">laptop</i>
                            <span class="nav-text">eCommerce</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                @if (!empty($isOnPaidPlan))
                                    <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'domains' ? 'active' : '' }}" id="nav-item-ecommerce-home">
                                        <a href="{{ route('apps.ecommerce.domains') }}">
                                            <i class="material-icons">public</i>
                                            <span class="nav-text">Domains</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'website' ? 'active' : '' }}" id="nav-item-ecommerce-website">
                                    <a href="{{ route('apps.ecommerce.website') }}">
                                        <i class="material-icons">folder</i>
                                        <span class="nav-text">Website</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'email' ? 'active' : '' }}" id="nav-item-ecommerce-email">
                                    <a href="{{ route('apps.ecommerce.emails') }}">
                                        <i class="material-icons">mail</i>
                                        <span class="nav-text">Email</span>
                                    </a>
                                </li>
                                <li  class="bold {{ !empty($selectedSubMenu) && $selectedSubMenu === 'blog' ? 'active' : '' }}" id="nav-item-ecommerce-blog">
                                    <a href="{{ route('apps.ecommerce.blog') }}">
                                        <i class="material-icons">comment</i>
                                        <span class="nav-text">Blog</span>
                                    </a>
                                </li>
                                <li  class="bold {{ !empty($selectedSubMenu) && $selectedSubMenu === 'adverts' ? 'active' : '' }}" id="nav-item-ecommerce-adverts">
                                    <a href="{{ route('apps.ecommerce.adverts') }}">
                                        <i class="material-icons">burst_mode</i>
                                        <span class="nav-text">Adverts</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'online-store' ? 'active' : '' }}" id="nav-item-ecommerce-store">
                                    <a href="{{ route('apps.ecommerce.store.dashboard') }}">
                                        <i class="material-icons">shopping_cart</i>
                                        <span class="nav-text">Online Store</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="no-padding" {{ !empty($currentPage) && $currentPage === 'finance' ? 'active' : '' }} v-if="enabledUis.indexOf('finance') !== -1">
                <ul class="collapsible" data-collapsible="accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect {{ !empty($currentPage) && $currentPage === 'finance' ? 'active' : '' }}" id="nav-item-finance">
                            <i class="material-icons">view_stream</i>
                            <span class="nav-text">Finance</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'accounts' ? 'active' : '' }}" id="nav-item-finance-accounts">
                                    <a href="{{ route('apps.finance') }}" class="active">
                                        <i class="material-icons">account_circle</i>
                                        <span class="nav-text">Accounts</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'entries' ? 'active' : '' }}" id="nav-item-finance-entries">
                                    <a href="{{ route('apps.finance.entries') }}" class="active">
                                        <i class="material-icons">format_align_center</i>
                                        <span class="nav-text">Entries</span>
                                    </a>
                                </li>
                                @if (!empty($isOnPaidPlan))
                                    <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'transtrak' ? 'active' : '' }}" id="nav-item-finance-transtrak">
                                        <a href="{{ route('apps.finance.transtrak') }}" class="active">
                                            <i class="material-icons">layers</i>
                                            <span class="nav-text">Transtrak</span>
                                        </a>
                                    </li>
                                    <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'reports' ? 'active' : '' }}" id="nav-item-finance-reports">
                                        <a href="{{ route('apps.finance.reports') }}" class="active">
                                            <i class="material-icons">report</i>
                                            <span class="nav-text">Report</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="no-padding {{ !empty($currentPage) && $currentPage === 'hr' ? 'active' : '' }}" v-if="enabledUis.indexOf('organisation') !== -1">
                <ul class="collapsible" data-collapsible="accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect {{ !empty($currentPage) && $currentPage === 'hr' ? 'active' : '' }}" id="nav-item-hr">
                            <i class="material-icons">domain</i>
                            <span class="nav-text">People</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'departments' ? 'active' : '' }}" id="nav-item-hr-departments">
                                    <a href="{{ route('business.departments') }}" class="active">
                                        <i class="material-icons">domain</i>
                                        <span class="nav-text">Departments</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'employees' ? 'active' : '' }}" id="nav-item-hr-employees">
                                    <a href="{{ route('business.employees') }}" class="active">
                                        <i class="material-icons">people_outline</i>
                                        <span class="nav-text">Employees</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'teams' ? 'active' : '' }}" id="nav-item-hr-teams">
                                    <a href="{{ route('business.teams') }}" class="active">
                                        <i class="material-icons">group</i>
                                        <span class="nav-text">Teams</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="no-padding {{ !empty($currentPage) && $currentPage === 'invoicing' ? 'active' : '' }}" v-if="enabledUis.indexOf('sales') !== -1">
                <ul class="collapsible" data-collapsible="accordion">
                    <li class="bold">
                        <a class="collapsible-header waves-effect {{ !empty($currentPage) && $currentPage === 'invoicing' ? 'active' : '' }}" id="nav-item-apps-invoicing">
                            <i class="material-icons">event</i>
                            <span class="nav-text">Sales</span>
                        </a>
                        <div class="collapsible-body">
                            <ul>
                                <li class="bold {{ !empty($selectedSubMenu) && $selectedSubMenu === 'categories' ? 'active' : '' }}" id="nav-item-apps-invoicing-categories">
                                    <a href="{{ route('apps.inventory.categories') }}" class="active">
                                        <i class="material-icons">storage</i>
                                        <span class="nav-text">Categories</span>
                                    </a>
                                </li>
                                <li class="bold {{ !empty($selectedSubMenu) && $selectedSubMenu === 'products' ? 'active' : '' }}" id="nav-item-apps-invoicing-products">
                                    <a href="{{ route('apps.inventory') }}" class="active">
                                        <i class="material-icons">style</i>
                                        <span class="nav-text">Products</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'invoice' ? 'active' : '' }}" id="nav-item-apps-invoicing-orders">
                                    <a href="{{ route('apps.invoicing.orders.new') }}">
                                        <i class="material-icons">monetization_on</i>
                                        <span>Simple Invoice</span>
                                    </a>
                                </li>
                                <li class="{{ !empty($selectedSubMenu) && $selectedSubMenu === 'orders' ? 'active' : '' }}" id="nav-item-apps-invoicing-orders">
                                    <a href="{{ route('apps.invoicing.orders') }}">
                                        <i class="material-icons">add_shopping_cart</i>
                                        <span>Orders</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </li>
        @endif
        <li class="li-hover" id="nav-item-professional"  v-if="enabledUis.indexOf('services') !== -1 || enabledUis.indexOf('vendors') !== -1">
            <p class="ultra-small margin more-text">Directory</p>
        </li>
        @if (empty($viewMode) || $viewMode === 'business')
            <li class="bold {{ !empty($currentPage) && $currentPage === 'professional_directory' ? 'active' : '' }}"
                id="nav-item-professional-directory" v-if="enabledUis.indexOf('services') !== -1">
                <a href="{{ route('directory') }}">
                    <i class="material-icons">book</i>
                    <span class="nav-text">Professional Services</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'vendor_directory' ? 'active' : '' }}"
                id="nav-item-vendor-directory" v-if="enabledUis.indexOf('vendors') !== -1">
                <a href="{{ route('directory.vendors') }}">
                    <i class="material-icons">assignment</i>
                    <span class="nav-text">Vendors</span>
                </a>
            </li>
        @elseif (!empty($viewMode) && $viewMode === 'professional' && !empty($dorcasUser) && $dorcasUser->is_professional)
            <li class="bold {{ !empty($currentPage) && $currentPage === 'access-grants' ? 'active' : '' }}" id="nav-item-professional-access-grants">
                <a href="{{ route('directory.access-grant') }}">
                    <i class="material-icons">markunread_mailbox</i>
                    <span class="nav-text">Access Grants</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'professional_profile' ? 'active' : '' }}" id="nav-item-professional-profile">
                <a href="{{ route('directory.profile') }}">
                    <i class="material-icons">contact_phone</i>
                    <span class="nav-text">Profile</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'professional_requests' ? 'active' : '' }}" id="nav-item-professional-requests">
                <a href="{{ route('directory.requests') }}">
                    <i class="material-icons">inbox</i>
                    <span class="nav-text">Requests</span>
                </a>
            </li>
        @elseif (!empty($viewMode) && $viewMode === 'vendor' && !empty($dorcasUser) && $dorcasUser->is_vendor)
            <li class="bold {{ !empty($currentPage) && $currentPage === 'vendor_profile' ? 'active' : '' }}" id="nav-item-vendor-profile">
                <a href="{{ route('directory.vendors.profile') }}">
                    <i class="material-icons">contact_phone</i>
                    <span class="nav-text">Profile</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'vendor_requests' ? 'active' : '' }}" id="nav-item-vendor-requests">
                <a href="{{ route('directory.requests') }}">
                    <i class="material-icons">inbox</i>
                    <span class="nav-text">Requests</span>
                </a>
            </li>
        @endif
        <li class="li-hover" v-if="!UiUsesGrants">
            <p class="ultra-small margin more-text">MORE</p>
        </li>
        @if (empty($viewMode) || $viewMode === 'business')
            <li class="bold {{ !empty($currentPage) && $currentPage === 'access-grants' ? 'active' : '' }}"
                id="nav-item-access-grants" v-if="!UiUsesGrants">
                <a href="{{ route('access-grants') }}">
                    <i class="material-icons">markunread_mailbox</i>
                    <span class="nav-text">Access Requests</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'app-store' ? 'active' : '' }}"
                id="nav-item-appstore" v-if="enabledUis.indexOf('appstore') !== -1">
                <a href="{{ route('app-store') }}">
                    <i class="material-icons">device_hub</i>
                    <span class="nav-text">Apps</span>
                </a>
            </li>
            <li class="bold {{ !empty($currentPage) && $currentPage === 'integrations' ? 'active' : '' }}"
                id="nav-item-integrations"  v-if="enabledUis.indexOf('integrations') !== -1">
                <a href="{{ route('integrations') }}">
                    <i class="material-icons">developer_board</i>
                    <span class="nav-text">Integrations</span>
                </a>
            </li>
        @endif
        <li class="bold {{ !empty($currentPage) && $currentPage === 'settings' ? 'active' : '' }}"
            id="nav-item-settings" v-if="enabledUis.indexOf('settings') !== -1">
            <a href="{{ route('settings') }}">
                <i class="material-icons">settings</i>
                <span class="nav-text">Settings</span>
            </a>
        </li>
    </ul>
    <a href="#" data-activates="slide-out" class="sidebar-collapse btn-floating btn-medium waves-effect waves-light hide-on-large-only gradient-45deg-light-blue-cyan gradient-shadow">
        <i class="material-icons">menu</i>
    </a>
</aside>
<!-- END LEFT SIDEBAR NAV-->
