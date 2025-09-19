<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="{{ __('Search for...') }}" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="{{ __('Search for...') }}" aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Language Toggle -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-globe fa-fw"></i>
                <span class="ml-2 d-none d-lg-inline text-gray-600 small">
                    {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="languageDropdown">
                <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                   href="{{ route('locale', 'en') }}" 
                   onclick="changeLanguage('en', this); return false;">
                    <i class="fas fa-flag-usa fa-sm fa-fw mr-2 text-gray-400"></i>
                    English
                    @if(app()->getLocale() == 'en')
                        <i class="fas fa-check fa-sm fa-fw ml-2 text-success"></i>
                    @endif
                </a>
                <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" 
                   href="{{ route('locale', 'ar') }}" 
                   onclick="changeLanguage('ar', this); return false;">
                    <i class="fas fa-flag fa-sm fa-fw mr-2 text-gray-400"></i>
                    العربية
                    @if(app()->getLocale() == 'ar')
                        <i class="fas fa-check fa-sm fa-fw ml-2 text-success"></i>
                    @endif
                </a>
            </div>

            <script>
            function changeLanguage(locale, element) {
                // Show loading state
                const originalText = element.innerHTML;
                element.innerHTML = '<i class="fas fa-spinner fa-spin fa-sm fa-fw mr-2"></i> Changing...';
                
                // Debug log
                console.log('Changing language to:', locale);
                
                // Simple approach: just redirect to the locale route
                window.location.href = '/locale/' + locale;
            }
            </script>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            @php
                $expiringDocuments = \App\Models\LegalDocument::expiringSoon(30)->get();
                $expiredDocuments = \App\Models\LegalDocument::expired()->get();
                $totalAlerts = $expiringDocuments->count() + $expiredDocuments->count();
            @endphp
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                @if($totalAlerts > 0)
                <span class="badge badge-danger badge-counter">{{ $totalAlerts > 9 ? '9+' : $totalAlerts }}</span>
                @endif
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    {{ trans('messages.Document Alerts') }}
                </h6>
                
                @if($totalAlerts > 0)
                    <!-- Expired Documents -->
                    @foreach($expiredDocuments->take(3) as $document)
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('legal-documents.show', $document) }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ trans('messages.Expired') }} - {{ $document->expiry_date->format('Y-m-d') }}</div>
                            <span class="font-weight-bold">{{ $document->document_name }}</span>
                        </div>
                    </a>
                    @endforeach
                    
                    <!-- Expiring Soon Documents -->
                    @foreach($expiringDocuments->take(3) as $document)
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('legal-documents.show', $document) }}">
                        <div class="mr-3">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{{ trans('messages.Expires in') }} {{ $document->days_until_expiry }} {{ trans('messages.days') }}</div>
                            <span class="font-weight-bold">{{ $document->document_name }}</span>
                        </div>
                    </a>
                    @endforeach
                    
                    <a class="dropdown-item text-center small text-gray-500" href="{{ route('legal-documents.index') }}">
                        {{ trans('messages.View All Documents') }}
                    </a>
                @else
                    <div class="dropdown-item text-center text-gray-500">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        {{ trans('messages.All documents are up to date') }}
                    </div>
                @endif
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                    {{ __('Message Center') }}
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{{ asset('assets/img/undraw_profile_1.svg') }}" alt="...">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">{{ __('Hi there! I am wondering if you can help me with a problem I have been having.') }}</div>
                        <div class="small text-gray-500">{{ __('Emily Fowler · 58m') }}</div>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">{{ __('Read More Messages') }}</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ __('Admin User') }}</span>
                <img class="img-profile rounded-circle" src="{{ asset('assets/img/undraw_profile.svg') }}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Profile') }}
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Settings') }}
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Activity Log') }}
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ __('Logout') }}
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->
