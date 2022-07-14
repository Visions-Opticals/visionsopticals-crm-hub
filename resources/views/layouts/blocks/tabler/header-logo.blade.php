                            <a class="header-brand" href="{{ url('/') }}">
                                <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : env('DORCAS_PARTNER_LOGO')}}" class="header-brand-img d-none d-md-inline" style="height: auto !important; width:auto !important; max-width: 150px !important; max-height: 35px !important;" alt="logo">
                            </a>
                            {{-- cdn('images/dorcas.jpeg')  --}}
                            <a class="header-brand" href="{{ url('/') }}">
                                <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : env('DORCAS_PARTNER_LOGO') }}" class="header-brand-img d-inline d-md-none" style="height: auto !important; width:auto !important; max-width: 75px !important; max-height: 35px !important;" alt="logo">
                            </a>