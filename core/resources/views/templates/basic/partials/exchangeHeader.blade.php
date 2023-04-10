@php
    $cryptos = getTopWallet();
@endphp
<style>
    @media (min-width: 1200px) {
        .container {
            max-width: 1471px;
        }
    }

    .nav-right .mode--toggle {
        display: none !important;
    }

    .mode--toggle {
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: right;
        cursor: pointer;
        color: #fff;
    }

    .select2-container {
        display: inline-grid !important;
    }

    .myBg {
        background: #212529;
    }

    .header.menu-fixed .header__bottom {
        background-color: #212529;
    }
</style>
<header class="header myBg">
    <div class="header__bottom">
        <div class="container">
            <nav class="navbar navbar-expand-xl p-0 align-items-center">
                <a class="site-logo site-title" href="{{ route('home') }}"><img
                        src="{{ getImage('assets/images/logoIcon/logo.png') }}" alt="site-logo"></a>
                <button class="navbar-toggler ms-auto shadow-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="menu-toggle"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav main-menu m-auto">
                        <li> <a href="{{ route('home') }}">@lang('Home')</a></li>

                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Buy')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $crypto)
                                    <li class="test_info"><a
                                            href="{{ route('buy.sell', ['buy', $crypto->code, 'all']) }}">{{ $crypto->code }}</a>
                                    </li>
                                @endforeach
                                <li class="test_info"><a href="{{ route('buy.sell', ['buy', 'btc', 'all']) }}">More</a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Sell')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $crypto)
                                    <li><a
                                            href="{{ route('buy.sell', ['sell', $crypto->code, 'all']) }}">{{ $crypto->code }}</a>
                                    </li>
                                @endforeach
                                <li><a href="{{ route('buy.sell', ['sell', 'btc', 'all']) }}">More</a></li>
                            </ul>
                        </li>
                        <li class="menu_has_children"><a href="#">@lang('Our App')</a>
                            <ul class="sub-menu">
                                <li>
                                    <img src="{{ getImage('assets/images/and_app.jpeg') }}">
                                </li>
                            </ul>
                            @auth
                            <li><a href="{{ route('user.advertisement.index') }}">@lang('Post An Ad')</a></li>
                            <li class="menu_has_children"><a href="javascript:void(0)">@lang('Trades')</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('user.trade.request.running') }}">@lang('Running')</a></li>
                                    <li><a href="{{ route('user.trade.request.completed') }}">@lang('Completed')</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ route('user.transfer') }}">@lang('P2P Transfer')</a></li>
                            {{--                            <li><a href="{{route('user.exchange.money')}}">@lang('Exchange')</a></li> --}}
                            <li class="menu_has_children"><a href="javascript:void(0)">@lang('Wallets')</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('user.wallets') }}">@lang('P2P Wallet')</a></li>
                                    <li><a href="{{ route('user.spot-wallet') }}">@lang('Spot Wallet')</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ route('user.transaction.index') }}">@lang('Transactions')</a></li>
                        @endauth


                        <!--

                @auth
                                                                                                                    <li><a href="{{ route('user.deposit.history') }}">@lang('Deposits')</a></li>
                                                                                                                    <li><a href="{{ route('user.withdraw.history') }}">@lang('Withdrawals')</a></li>
                        @endauth -->

                        @auth
                            <li class="menu_has_children"><a href="javascript:void(0)">@lang('More')</a>
                                <ul class="sub-menu">
                                    @foreach ($pages as $k => $data)
                                        <li><a href="{{ route('pages', [$data->slug]) }}"
                                                class="nav-link">{{ __($data->name) }}</a></li>
                                    @endforeach
                                    <li><a href="{{ route('ticket') }}">@lang('Support')</a></li>
                                    <li><a href="{{ route('user.deposit.history') }}">@lang('Deposits')</a></li>
                                    <li><a href="{{ route('user.withdraw.history') }}">@lang('Withdrawals')</a></li>
                                    <li><a href="{{ route('user.referral.commissions.trade') }}">@lang('Referral')</a>
                                    </li>
                                    <li><a href="{{ route('user.change.password') }}">@lang('Password')</a></li>
                                    <li><a href="{{ route('user.profile.setting') }}">@lang('Profile Setting')</a></li>
                                    <li><a href="{{ route('user.twofactor') }}">@lang('2FA Security')</a></li>
                                    <li><a href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                                </ul>
                            </li>

                        @endauth
                        <li><a href="{{ url('exchange') }}">@lang('Spot Trading')</a></li>
                    </ul>

                    <div class="nav-right">
                        <div class="mode--toggle d-sm-none"><i class="fas fa-moon"></i></div>
                        @if (!blank($language))
                            <select class="language-select langSel rounded-2 h-100">
                                @foreach ($language as $item)
                                    <option value="{{ $item->code }}"
                                        @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}
                                    </option>
                                @endforeach

                            </select>
                        @endif

                        <ul class="account-menu ms-3">
                            @auth
                                <li>
                                    <a href="{{ route('user.home') }}" class="btn btn--base btn-sm">@lang('Dashboard')</a>
                                </li>
                            @else
                                <li class="icon"><i class="las la-user"></i>
                                    <ul class="account-submenu">
                                        <li><a href="{{ route('user.login') }}">@lang('Login')</a></li>
                                        <li><a href="{{ route('user.register') }}">@lang('Registration')</a></li>
                                    </ul>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div><!-- header__bottom end -->
</header>
