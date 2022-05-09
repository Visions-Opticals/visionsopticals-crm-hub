<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section('head_meta')
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <meta name="author" content="Visacheck" />
    @show
    <title>@section('head_title'){{ $page['title'] ?? 'vPanel' }} | {{ config('app.name') }}@show</title>
    <!-- Favicons-->
    @include('layouts.blocks.favicons')
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/bootstrap-table.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/base.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/type1.css') }}">
    <link rel="stylesheet" href="{{ cdn('apps/vpanel/css/plugins.css') }}">
    @yield('custom_css')
    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    @yield('head_css')
</head>
<body @yield('body_class')>
@section('body')
    <div class="dash_wrap">
        @section('body_sidebar')
            <div class="dash_sidebar">
                @section('body_sidebar_header')
                    @include('vpanel.layouts.blocks.sidebar-header')
                @show
                <div class="content">
                    @section('body_sidebar_nav')
                        @include('vpanel.layouts.blocks.sidebar-nav')
                    @show
                </div>
            </div>
        @show
        @section('body_content')
            <div class="dash_content-wrapper">
                @section('body_content_header')
                    @include('vpanel.layouts.blocks.content-header')
                @show
                @section('body_content_main')
                    <div class="dash_content-inner">
                        <div class="dash_content-title">{{ $title['title'] ?? '' }}</div>
                        <div class="dash_content">
                            @section('body_content_main_header')
                                <header></header>
                            @show
                            @section('body_content_main_container')
                                <div class="scrollable">
                                    <div class="vgap-2x"></div>
                                    @yield('body_content_main_container_content')
                                </div>
                            @show
                        </div>
                    </div>
                @show
            </div>
        @show
    </div>
@show
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ cdn('apps/vpanel/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/axios.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/vue.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/popper.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/bootstrap.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/sweetalert.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/polyfill.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/mlib.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/moment.min.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/combodate.js') }}"></script>
<script src="{{ cdn('apps/vpanel/js/vpanel.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
@if (app()->environment() === 'production')
    @include('layouts.blocks.production-js')
@endif
<script type="text/javascript">
    $(function() {
        $('.combodate-date').combodate({customClass: 'form-control', smartDays: true});
        $('.bootstrap-table').bootstrapTable({
            buttonsClass: 'outline',
            iconsPrefix: '',
            icons: {
                paginationSwitchDown: 'ion-ios-arrow-down',
                paginationSwitchUp: 'ion-ios-arrow-up',
                refresh: 'ion-ios-refresh',
                toggle: 'ion-ios-list',
                columns: 'ion-ios-grid',
                detailOpen: 'ion-ios-add',
                detailClose: 'ion-ios-remove'
            }
        });
    });

    @if (!empty($uiToast))
    M.toast({!! json_encode($uiToast) !!});
    @endif
    let $searchVuew = new Vue({
        el: '#search-bar',
        data: {
            query: ''
        },
        methods: {
            search: function () {
                console.log('Search initiated...');
            }
        }
    });
    new Vue({
        el: '#sidebar-nav',
        data: {
            selectedMenu: '{{ $selectedMenu ?? '' }}',
            partner: {!! json_encode(!empty($partner) ? $partner : []) !!},
            company: {!! json_encode(!empty($business) ? $business : []) !!},
            user: {!! json_encode(!empty($dorcasUser) ? $dorcasUser : []) !!},
        }
    });
</script>
@yield('body_js')
</body>
</html>
