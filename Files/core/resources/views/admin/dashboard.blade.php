@extends('admin.layouts.app')

@php
$walletImage = fileManager()->crypto();
@endphp

@section('panel')
    @if (@json_decode($general->system_info)->version > systemDetails()['version'])
        <div class="row">
            <div class="col-md-12">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">
                        <h3 class="card-title"> @lang('New Version Available') <button class="btn btn--dark float-end">@lang('Version') {{ json_decode($general->system_info)->version }}</button> </h3>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-dark">@lang('What is the Update ?')</h5>
                        <p>
                            <pre class="f-size--24">{{ json_decode($general->system_info)->details }}</pre>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (@json_decode($general->system_info)->message)
        <div class="row">
            @foreach (json_decode($general->system_info)->message as $msg)
                <div class="col-md-12">
                    <div class="alert border border--primary" role="alert">
                        <div class="alert__icon bg--primary"><i class="far fa-bell"></i></div>
                        <p class="alert__message">@php echo $msg; @endphp</p>
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @php
        $fiatCondition = Carbon\Carbon::parse($general->fiat_cron)->diffInSeconds() >= 900;
        $cryptoCondition = Carbon\Carbon::parse($general->crypto_cron)->diffInSeconds() >= 900;
    @endphp

    @if ($fiatCondition || $cryptoCondition)
        <div class="d-flex gap-3 mb-3">
            @if ($fiatCondition)
                <div class="bg--red-shade border border--danger p-3 rounded flex-fill">
                    <h4 class="text--danger text-center">
                        @lang('Last Fiat Cron Executed'): {{ diffForHumans($general->fiat_cron) }}
                    </h4>
                </div>
            @endif
            @if ($cryptoCondition)
                <div class="bg--red-shade border border--danger p-3 rounded flex-fill">
                    <h4 class="text--danger text-center">@lang('Last Crypto Cron Runs'): {{ diffForHumans($general->crypto_cron) }}</h4>
                </div>
            @endif
        </div>
    @endif

    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--primary has-link overflow-hidden box--shadow2">
                <a href="{{ route('admin.users.all') }}" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-users f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Total Users')</span>
                            <h2 class="text-white">{{ $widget['total_users'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--success has-link box--shadow2">
                <a href="{{ route('admin.users.active') }}" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-user-check f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Active Users')</span>
                            <h2 class="text-white">{{ $widget['verified_users'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--danger has-link box--shadow2">
                <a href="{{ route('admin.users.email.unverified') }}" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="lar la-envelope f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Email Unverified Users')</span>
                            <h2 class="text-white">{{ $widget['email_unverified_users'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg--red has-link box--shadow2">
                <a href="{{ route('admin.users.mobile.unverified') }}" class="item-link"></a>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="las la-comment-slash f-size--56"></i>
                        </div>
                        <div class="col-8 text-end">
                            <span class="text-white text--small">@lang('Mobile Unverified Users')</span>
                            <h2 class="text-white">{{ $widget['mobile_unverified_users'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                <i class="lar la-credit-card overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="lar la-credit-card"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ __($widget['total_withdraw_approved']) }}</h3>
                    <p class="text-white">@lang('Approved Withdrawal')</p>
                </div>
                <a href="{{ route('admin.withdraw.log') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                <i class="las la-sync overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-sync"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['total_withdraw_pending'] }}</h3>
                    <p class="text-white">@lang('Pending Withdrawals')</p>
                </div>
                <a href="{{ route('admin.withdraw.pending') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--14">
                <i class="las la-times-circle overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-times-circle"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['total_withdraw_rejected'] }}</h3>
                    <p class="text-white">@lang('Rejected Withdrawals')</p>
                </div>
                <a href="{{ route('admin.withdraw.rejected') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                <i class="la la-bank overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="la la-bank"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['total_withdraw'] }}</h3>
                    <p class="text-white">@lang('Total Withdrawal')</p>
                </div>
                <a href="{{ route('admin.withdraw.log') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <div class="row gy-4 mt-2">
        <div class="col-md-12">
            <h4>@lang('Deposit Summary')</h4>
        </div>
    </div>

    <div class="row gy-4 mt-2">
        @foreach ($deposits as $deposit)
            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <div class="widget-two__icon b-radius--5 text--success">
                        <img src="{{ getImage($walletImage->path . '/' . $deposit->image, $walletImage->size) }}" alt="image">
                    </div>
                    <div class="widget-two__content">
                        <h3>{{ showAmount($deposit->deposits_sum_amount, 8) }} {{ __($deposit->code) }}</h3>
                        <span>@lang('Charge')</span>
                        <i class="fas fa-arrow-right text--danger"></i>
                        <span class="text--danger">{{ showAmount($deposit->deposits_sum_charge, 8) }} {{ __($deposit->code) }}</span>
                    </div>
                    <a href="{{ route('admin.deposit.list') }}" class="widget-two__btn border border--success btn-outline--success">@lang('View All')</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--19">
                <i class="lab la-adversal overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="lab la-adversal"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['totalAd'] }}</h3>
                    <p class="text-white">@lang('Total Adveretisements')</p>
                </div>
                <a href="{{ route('admin.ad.index') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary">
                <i class="las la-exchange-alt overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="las la-exchange-alt"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['totalTrade'] }}</h3>
                    <p class="text-white">@lang('Total Trades')</p>
                </div>
                <a href="{{ route('admin.trade.index') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--1">
                <i class="lab la-bitcoin overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--primary">
                    <i class="lab la-bitcoin"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['totalCrypto'] }}</h3>
                    <p class="text-white">@lang('Total Cryptocurrency')</p>
                </div>
                <a href="{{ route('admin.crypto.index') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <div class="widget-two style--two box--shadow2 b-radius--5 bg--success">
                <i class="las la-coins overlay-icon text--white"></i>
                <div class="widget-two__icon b-radius--5 bg--success">
                    <i class="las la-coins"></i>
                </div>
                <div class="widget-two__content">
                    <h3 class="text-white">{{ $widget['totalFiat'] }}</h3>
                    <p class="text-white">@lang('Total Fiat Currency')</p>
                </div>
                <a href="{{ route('admin.fiat.currency.index') }}" class="widget-two__btn">@lang('View All')</a>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <div class="row gy-4 mt-2">
        <div class="col-md-12">
            <h4>@lang('Withdrawal Summary')</h4>
        </div>
    </div>

    <div class="row gy-4 mt-2">
        @foreach ($withdrawals as $withdrawal)
            <div class="col-xxl-3 col-sm-6">
                <div class="widget-two box--shadow2 b-radius--5 bg--white">
                    <div class="widget-two__icon b-radius--5 text--success">
                        <img src="{{ getImage($walletImage->path . '/' . $withdrawal->image, $walletImage->size) }}" alt="image">
                    </div>
                    <div class="widget-two__content">
                        <h3>{{ showAmount($withdrawal->withdrawals_sum_amount, 8) }} {{ __($withdrawal->code) }}</h3>
                        <span>@lang('Charge')</span>
                        <i class="fas fa-arrow-right text--danger"></i>
                        <span class="text--danger">{{ showAmount($withdrawal->withdrawals_sum_charge, 8) }} {{ __($withdrawal->code) }}</span>
                    </div>
                    <a href="{{ route('admin.withdraw.log') }}" class="widget-two__btn border border--success btn-outline--success">@lang('View All')</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Cron Modal --}}
    <div id="cronModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Cron Job Setting Instruction')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="cron mb-2 text-justify">@lang('To Automate the process of deactive the expired promotional featured ads, you need to set the cron job. Set The cron time as minimum as possible.')</p>
                    <label class="w-100 fw-bold">@lang('Fiat Currency Cron Command')</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control copyText" value="curl -s {{ route('cron.fiat.rate') }}" readonly>
                        <button class="input-group-text btn btn--primary copyBtn" data-clipboard-text="curl -s {{ route('cron.fiat.rate') }}" type="button"><i class="la la-copy"></i></button>
                    </div>

                    <label class="w-100 fw-bold">@lang('Cryptocurrency Cron Command')</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control copyText" value="curl -s {{ route('cron.crypto.rate') }}" readonly>
                        <button class="input-group-text btn btn--primary copyBtn" data-clipboard-text="curl -s {{ route('cron.crypto.rate') }}" type="button"><i class="la la-copy"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style')
    <style>
        .bg--red-shade {
            background-color: #f3d6d6;
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script>
        "use strict";

        @if ($fiatCondition || $cryptoCondition)
            (function($) {
                var cronModal = new bootstrap.Modal(document.getElementById('cronModal'));
                cronModal.show();

                $('.copyBtn').on('click', function() {
                    var copyText = $(this).siblings('.copyText')[0];
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    copyText.blur();
                    $(this).addClass('copied');
                    setTimeout(() => {
                        $(this).removeClass('copied');
                    }, 1500);
                });
            })(jQuery);
        @endif

        $('.copy-address').on('click', function() {
            var clipboard = new ClipboardJS('.copy-address');
            notify('success', 'Copied : ' + $(this).data('clipboard-text'));
        });

        var ctx = document.getElementById('userBrowserChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_browser_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_browser_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                maintainAspectRatio: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });


        var ctx = document.getElementById('userOsChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_os_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_os_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(0, 0, 0, 0.05)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            },
        });


        // Donut chart
        var ctx = document.getElementById('userCountryChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_country_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_country_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
                        '#D980FA',
                        '#fccbcb',
                        '#45aaf2',
                        '#05dfd7',
                        '#FF00F6',
                        '#1e90ff',
                        '#2ed573',
                        '#eccc68',
                        '#ff5200',
                        '#cd84f1',
                        '#7efff5',
                        '#7158e2',
                        '#fff200',
                        '#ff9ff3',
                        '#08ffc8',
                        '#3742fa',
                        '#1089ff',
                        '#70FF61',
                        '#bf9fee',
                        '#574b90'
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,

                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
    </script>
@endpush
