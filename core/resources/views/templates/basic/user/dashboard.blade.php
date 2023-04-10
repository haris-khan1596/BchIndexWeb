@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $kycContent = getContent('kyc.content', true);
        $walletImage = fileManager()->crypto();
        $profileImage = fileManager()->userProfile();
    @endphp
    <style>
        .select2-container {
            display: inline-grid !important;
        }
        .ref_options{
            display: none;
            position:fixed;
            left: 82%;
            top: 24%;
            padding: 20px;
            flex-wrap: wrap;
            background-color: #ffffff;
            border-radius: 5px;
            transition: all 0.3s;
            overflow: hidden;
            align-items: center;
            border: 1px solid #ebebeb;
            z-index: 1;
        }
        .ref_options ul li{
            cursor: pointer;
            padding: 2px;
        }
        .ref_options ul li:hover{
            font-weight: bold;
            background-color: #e0e0e0;
            border:#e0e0e0 1px solid ;
        }
        @media (max-width: 575px) {
            .ref_options{
                left: 58%;
                top: 12%;
            }
        }
    </style>
    <section class="pt-60 pb-60 section--bg">
        <div class="container">

            @if ($user->kv == 0)
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification Required')</h4>
                    <hr>
                    <p class="mb-0">{{ __(@$kycContent->data_values->kyc_required) }} <a
                            href="{{ route('user.kyc.form') }}" class="text--base">@lang('Click Here to Verify')</a>
                    </p>
                </div>
            @elseif($user->kv == 2)
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification Pending')</h4>
                    <hr>
                    <p class="mb-0">{{ __(@$kycContent->data_values->kyc_pending) }} <a
                            href="{{ route('user.kyc.data') }}" class="text--base">@lang('See KYC Data')</a></p>
                </div>
            @endif


            <div class="row gy-4 flex-wrap-reverse">
                <div class="col-xl-3 col-lg-4">
                    <div class="profile-sidebar">
                        <!--<div class="profile-sidebar__widget">-->
                        <!--    <div class="profile-author">-->
                        <!--        <div class="thumb">-->
                    <!--            <img src="{{ getImage($profileImage->path . '/' . @$user->image, null, true) }}" alt="image">-->
                        <!--        </div>-->
                        <!--        <div class="content text-center">-->
                    <!--            <h5>{{ $user->username }}</h5>-->
                        <!--        </div>-->
                    <!--        <a href="{{ route('user.profile.setting') }}" class="border-btn d-block text-center btn-md mt-4">@lang('Profile Setting')</a>-->
                    <!--        <a href="{{ route('user.advertisement.index') }}" class="border-btn d-block text-center btn-md mt-3">@lang('Advertisements')</a>-->
                    <!--        <a href="{{ route('user.trade.request.running') }}" class="border-btn d-block text-center btn-md mt-3">@lang('Running Trades')</a>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="profile-sidebar__widget">
                            <h4 class="profile-sidebar__title">@lang('Estimated Balance')</h4>
{{--                            @php--}}
{{--                                $tem = 0;--}}
{{--                                if(isset($wallets) && !empty($wallets)){--}}
{{--                                   // dd($wallets);--}}
{{--                                    $tmp = 0;--}}
{{--                                    foreach ($wallets as $wallet){--}}
{{--                                        $tmp = $tmp+$wallet->balance;--}}
{{--                                        $tem = showAmount($tmp, 2);--}}
{{--                                    }--}}
{{--                                    }--}}
{{--                            @endphp--}}
                            <h2 class="d-widget__amount"> USD {{ showAmount(@$est_balance[0]->est_balance,2) }}</h2>
                        </div>
                        <div class="profile-sidebar__widget">
                            <h4 class="profile-sidebar__title">@lang('Verifications')</h4>
                            <ul class="profile-verify-list">
                                <li class="@if ($user->ev) verified @else unverified @endif"><i
                                        class="las la-envelope"></i>
                                    @if ($user->ev)
                                        @lang('Email Verified')
                                    @else
                                        @lang('Email Unverified')
                                    @endif
                                </li>

                                <li class="@if ($user->sv) verified @else unverified @endif"><i
                                        class="las la-mobile-alt"></i>
                                    @if ($user->sv)
                                        @lang('SMS Verified')
                                    @else
                                        @lang('SMS Unverified')
                                    @endif
                                </li>

                                <li class="@if ($user->kv == 1) verified @else unverified @endif"><i
                                        class="las la-user-check"></i>
                                    @if ($user->kv == 1)
                                        @lang('KYC Verified')
                                    @else
                                        @lang('KYC Unverified')
                                    @endif
                                </li>
                            </ul>
                        </div>

                        <div class="profile-sidebar__widget">
                            <h4 class="profile-sidebar__title">@lang('Informations')</h4>
                            <ul class="profile-info-list">
                                <li>
                                    <span class="caption">@lang('Joined On')</span>
                                    <span class="value">{{ showDateTime($user->created_at, 'F Y') }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Advertisements')</span>
                                    <span class="value">{{ $totalAdd }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Completed Trade') </span>
                                    <span class="value">{{ $user->completed_trade }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8">

                    <div class="row gy-4">
                        @if(isset($wallets) && !empty($wallets))
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div style="float: right">
                                <button class="mb-2 btn btn--base btn-sm" id="fff" name="fff">0% Fee</button>
                                
                                <button class="mb-2 btn btn--base btn-sm" id="refresh" name="refresh">Refresh</button>
                                <div class="ref_options">
                                    <ul>
                                        <li class="nn">Not now</li>
                                        <li class="s5">5s to refresh</li>
                                        <li class="s10">10s to refresh</li>
                                        <li class="s20">20s to refresh</li>
                                    </ul>
                                </div>
                            </div>
                            <label>@lang('Filter')</label>
                            <div class="input-group">
                                <select class="select" id="wallets_id" name="wallets_id">
                                    <option value="">Search Wallets</option>
                                    @foreach ($wallets as $wallet)
                                    @if(isset($wallet->crypto->id))
                                    
                                    <?php
                                    if($wallet->crypto->rate==0){
                                        $wallet->crypto->rate = 1;
                                    }
                                    ?>
                                        <option
                                            value="{{ route('user.transaction.index') }}?crypto={{ $wallet->crypto->id }}">
                                            {{ $wallet->crypto->code }} {{ showAmount($wallet->balance/$wallet->crypto->rate, 6) }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($wallets_cards) && !empty($wallets_cards))
                            @foreach ($wallets_cards as $wallet)
                            @if(isset($wallet->crypto->id))
                            <?php
                                    if($wallet->crypto->rate==0){
                                        $wallet->crypto->rate = 1;
                                    }
                                    ?>
                                <div class="col-xl-4 col-md-6 d-widget-item">
                                    <a class="d-block"
                                       href="{{ route('user.transaction.index') }}?crypto={{ $wallet->crypto->id }}">
                                        <div class="d-widget">
                                            <div class="d-widget__icon">
                                                <img
                                                    src="{{ getImage($walletImage->path . '/' . $wallet->crypto->image, $walletImage->size) }}"
                                                    alt="">
                                            </div>
                                            <div class="d-widget__content">
                                                <p class="d-widget__caption">{{ __($wallet->crypto->code) }}</p>
                                                <h2 class="d-widget__amount">{{ showAmount($wallet->balance/$wallet->crypto->rate, 6) }}</h2>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        @endif
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <label>@lang('Referral Link')</label>
                            <div class="input-group">
                                <input type="text" name="key"
                                       value="{{ route('user.register', [auth()->user()->username]) }}"
                                       class="form-control bg-white" id="referralURL" readonly>

                                <button class="input-group-text bg--base text-white border-0 copytext" id="copyBoard">
                                    <i class="lar la-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <h4 class="my-3">@lang('Latest Advertisements')</h4>

                    @include($activeTemplate . 'partials.user_ads_table')
                </div>
            </div>


        </div>
    </section>
@endsection

@push('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function ($) {
            "use strict";
            $('.select').select2();
            $('#refresh').on('click', function () {
                $('.ref_options').toggle();
            });
            //nn
            $(".nn").on('click', function () {
                $('.ref_options').toggle();
                location.reload(true);
            });
            $(".s5").on('click', function () {
                $('.ref_options').toggle();
                setupInterval(function () {
                    location.reload(true);
                    console.log('5000');
                }, 5000,'','');
            });
            $(".s10").on('click', function () {
                $('.ref_options').toggle();
                setupInterval(function () {
                    location.reload(true);
                    console.log('10000');
                }, 10000,'','');
            });
            $(".s20").on('click', function () {
                $('.ref_options').toggle();
                setupInterval(function () {
                    location.reload(true);
                    console.log('20000');
                }, 20000,'','');
            });
            console.log(localStorage.getItem('_timeInMs_'));
            function setupInterval (callback, interval, name, intervalId) {
                var key = '_timeInMs_' + (name || '');
                var now = Date.now();
                var timeInMs = localStorage.getItem(key);
                var executeCallback = function () {
                    localStorage.setItem(key, Date.now());
                    callback();
                }

                if (timeInMs) { // User has visited
                    var time = parseInt(timeInMs);
                    var delta = now - time;
                    if (delta > interval) { // User has been away longer than interval
                        intervalId = setInterval(executeCallback, interval);
                    } else { // Execute callback when we reach the next interval
                        setTimeout(function () {
                            intervalId = setInterval(executeCallback, interval);
                            executeCallback();
                        }, interval - delta);
                    }
                } else {
                    intervalId = setInterval(executeCallback, interval);
                }
                console.log(interval);
                console.log(intervalId);
                localStorage.setItem(key, now);
            }

            $('.copytext').on('click', function () {
                var copyText = document.getElementById("referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });

            $('#wallets_id').on('change', function () {
                let url = $(this).val();
                if (url != '') {
                    window.location = url;
                }
            });

        })(jQuery);
    </script>
@endpush

@push('style-lib')
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
@endpush
