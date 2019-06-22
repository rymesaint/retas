@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>@yield('title')</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <div class="icon">
                    <i class="material-icons col-red">shopping_cart</i>
                </div>
                <div class="content">
                    <div class="text">{{ __('TOTAL ORDER') }}</div>
                    <div class="number count-to" data-from="0" data-to="125" data-speed="1000" data-fresh-interval="20">125</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <div class="icon">
                    <i class="material-icons col-orange">restaurant_menu</i>
                </div>
                <div class="content">
                    <div class="text">{{ __('TOTAL MENU') }}</div>
                    <div class="number count-to" data-from="0" data-to="{{ $menuCount }}" data-speed="1000" data-fresh-interval="20">{{ $menuCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <div class="icon">
                        <i class="material-icons col-green">store_mall_directory</i>
                    </div>
                    <div class="content">
                        <div class="text">{{ __('TOTAL BRANCH') }}</div>
                        <div class="number count-to" data-from="0" data-to="{{ $branchCount }}" data-speed="1000" data-fresh-interval="20">{{ $branchCount }}</div>
                    </div>
                </div>
            </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <div class="icon">
                    <span class="chart chart-line" data-chartcolor="brown">9,4,6,5,6,4,7,3</span>
                </div>
                <div class="content">
                    <div class="text">{{ __('MONTHLY SALES') }}</div>
                    <div class="number">Rp {{ $salesCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <div class="row clearfix">
                        <div class="col-xs-12 col-sm-6">
                            <h2>{{ __('DAILY SALES') }}</h2>
                        </div>
                    </div>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">{{ __('Show Order History') }}</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div id="chart_daily_sales" class="dashboard-flot-chart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/jquery-countto/jquery.countTo.js') }}"></script>
    <script src="{{ asset('vendor/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('vendor/morrisjs/morris.js') }}"></script>
    <script src="{{ asset('vendor/chartjs/Chart.bundle.js') }}"></script>
    <script src="{{ asset('vendor/flot-charts/jquery.flot.js') }}"></script>
    <script src="{{ asset('vendor/flot-charts/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('vendor/flot-charts/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('vendor/flot-charts/jquery.flot.categories.js') }}"></script>
    <script src="{{ asset('vendor/flot-charts/jquery.flot.time.js') }}"></script>
    <script src="{{ asset('vendor/jquery-sparkline/jquery.sparkline.js') }}"></script>
    <script src="{{asset('js/index.js') }}"></script>
    <script src="{{asset('js/widgets/infobox/infobox-5.js') }}"></script>
@endpush
