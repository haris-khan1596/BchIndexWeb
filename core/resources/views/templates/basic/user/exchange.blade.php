<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://code.highcharts.com/css/stocktools/gui.css">
    <link rel="stylesheet" type="text/css" href="https://code.highcharts.com/css/annotations/popup.css">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php?color1=' . $general->base_color) }}">
    {{-- <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet"
        href="https://unpkg.com/lightweight-charts@3.3.0/dist/lightweight-charts.standalone.production.min.css" />
    <script src="https://unpkg.com/lightweight-charts@3.3.0/dist/lightweight-charts.standalone.production.js"></script>

    @stack('style-lib')
    @stack('style')
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .tab__list {
            display: flex;
            justify-content: space-between;
        }

        .section {
            padding-top: 10px;
        }

        .tab__list {
            padding-bottom: 15px;
        }

        .tab__content {
            padding: 5px;
            color: #afafaf;
        }

        .tab__content-item {
            display: none;
        }

        .tab__content-item.is--active {
            display: block;
        }

        .tab__content-title {
            padding: 24px 24px 24px 0;
        }

        .tab .tab .tab__item {
            font-size: 16px;
        }

        .tab .tab .tab__content {
            padding: 0;
        }

        #buy {
            color: #3B926C;
        }

        #buy {
            overflow: hidden;
            cursor: pointer;
            text-shadow: 0 0 0.5px;
            letter-spacing: 1px;
            transition: 0.2s;
            font-size: 18px;
            position: relative;
        }

        #buy:before {
            content: "";
            position: absolute;
            top: 95%;
            left: 0;
            height: 2px;
            width: 100%;
            background: #3B926C;
            transition: 0.2s;
            visibility: hidden;
            opacity: 0;
        }

        #buy.is--active {
            position: relative;
        }

        #buy.is--active:before {
            visibility: visible;
            opacity: 1;
        }

        #sell {
            color: #F44336;
        }

        #sell {
            overflow: hidden;
            cursor: pointer;
            text-shadow: 0 0 0.5px;
            letter-spacing: 1px;
            transition: 0.2s;
            font-size: 18px;
            position: relative;
        }

        #sell:before {
            content: "";
            position: absolute;
            top: 95%;
            left: 0;
            height: 2px;
            width: 100%;
            background: #F44336;
            transition: 0.2s;
            visibility: hidden;
            opacity: 0;
        }

        #sell.is--active {
            position: relative;
        }

        #sell.is--active:before {
            visibility: visible;
            opacity: 1;
        }

        .subItem {
            overflow: hidden;
            cursor: pointer;
            text-shadow: 0 0 0.5px;
            letter-spacing: 1px;
            transition: 0.2s;
            font-size: 18px;
            position: relative;
        }

        .subItem:before {
            content: "";
            position: absolute;
            top: 95%;
            left: 0;
            height: 2px;
            width: 100%;
            background: #afafaf;
            transition: 0.2s;
            visibility: hidden;
            opacity: 0;
        }

        .subItem.is--active {
            position: relative;
        }

        .subItem.is--active:before {
            visibility: visible;
            opacity: 1;
        }

        .subItem:hover {
            color: #3B926C;
            cursor: pointer;
        }

        .currency_display {
            color: #3B926C;
            font-size: 14px;
        }

        .myPt {
            padding-top: 150px;
        }

        #coin_pair_input {
            color: #3B926C;
            border: 1px solid #3B926C;
            caret-color: #3B926C;
        }

        .CP {
            cursor: pointer;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .buy-order {
            color: #3B926C;
        }

        .sell-order {
            color: #F44336;
        }

        #order-book td {
            font-size: 12px;
            /* text-align: center; */
        }

        #order-book th {
            font-size: 16px;
            /* text-align: center; */
        }

        #order-book .bitcoin-price {
            font-size: 20px;
            font-weight: bold;
        }

        #order-book .price-up {
            color: #3B926C;
        }

        #order-book .price-down {
            color: #F44336;
        }

        #order-book .bitcoin-price {
            text-align: center;
        }

        #order td {
            font-size: 14px;
        }

        #order_wrapper .dataTables_filter input[type="search"],
        #order_wrapper .dataTables_length select,
        #order_wrapper .dataTables_info {
            color: #3B926C;
        }

        .EXTRA {
            font-size: 24px;
        }

        .parent {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            flex: 1;
            text-align: center;
        }

        .extra {
            flex: 1;
            text-align: left;
        }

        .time {
            flex: 1;
            text-align: right;
        }

        .t {
            font-size: 20px;
        }

        .logo-img {
            width: 70px;
            /* Change this value to adjust the width */
            height: auto;
            /* Automatically adjust the height to maintain aspect ratio */
            filter: grayscale(60%);
            /* Convert the image to grayscale */
        }

        .price-info {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
            padding: 20px;
            border-radius: 10px;

        }

        .price-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .price-label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .price-value {
            font-size: 18px;
            color: #555;
        }

        .exPrice {
            font-size: 22px;
        }

        .section--bg {
            background-color: #212529;
        }

        .section--bg--light {
            background-color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="preloader-container">
            <span class="animated-preloader"></span>
        </div>
    </div>

    <div class="page-wrapper">
        @if (!Request::routeIs('user.register') && !Request::routeIs('user.login'))
            @include($activeTemplate . 'partials.exchangeHeader')
        @endif

        <!-- ************************************************************ Exchange section ********************************************************************** -->
        @php
        if(auth()->check()){
            $cryptoBalance1 = number_format($cryptoBalance1, 8, '.', '');
            $cryptoBalance2 = number_format($cryptoBalance2, 8, '.', '');
            $Bprice = $cryptoCurrency1->rate / $cryptoCurrency2->rate;
            $BuyPrice = number_format($Bprice, 8, '.', '');
            }
            else{
                $cryptoBalance1 = '0.00';
            $cryptoBalance2 = '0.00';
            $Bprice = $cryptoCurrency1->rate / $cryptoCurrency2->rate;
            $BuyPrice = number_format($Bprice, 8, '.', '');
            $allOrders= array();
            $login ="You are not Logged In";
            }
        @endphp
        <section class="pb-60 myPt section--bg" id="s">
            <div class="container">

                <div id="button float-end">
                    <div class="row justify-content-between">
                        <div class="col col-lg-2 ">
                            {{-- Select Pair: --}}
                            <div class="tab__list">
                                <form method="POST" id="exchange-form" action="{{ url('exchange') }}">
                                    @csrf
                                    <select name="pair" id="coin_pair_input" class="form-select bg-transparent"
                                        aria-label="Default select example"
                                        onchange="document.getElementById('exchange-form').submit()">
                                        {{-- <option selected>Select Pair</option> --}}
                                        @foreach ($coinPairs as $coin_pair)
                                            @if ($pair == $coin_pair->id)
                                                <option value="{{ $coin_pair->id }}" selected>{{ $coin_pair->name }}
                                                </option>
                                            @else
                                                <option value="{{ $coin_pair->id }}">{{ $coin_pair->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="form-check form-switch mx-4 col d-flex justify-content-end">
                            <div>
                                <input class="form-check-input p-2" type="checkbox" role="switch"
                                    id="flexSwitchCheckChecked" checkedu onclick="myFunction()">Switch
                                Theme</input>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="row gap-3 gap-xl-5">
                            <div class="col-12 col-lg-3 border border-secondary">
                                <div class="col">
                                    {{-- <div>Order book </div> --}}
                                    <div>
                                        <table id="order-book" class="table table-borderless table-dark">
                                            <thead>
                                                <tr>
                                                    <th id="headingPrice">Price ({{ $cryptoCurrency2->code }})</th>
                                                    <th id="headingAmount">Amount ({{ $cryptoCurrency1->code }})</th>
                                                    <th id="headingTotal">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($orderBook as $order)
                                                    @if ($order->action == 'sell')
                                                        <!-- Sell orders -->
                                                        <tr class="sell-order">
                                                            <td class="price">
                                                                {{ number_format($order->price, 8, '.', '') }}</td>
                                                            <td class="amount">
                                                                {{ number_format($order->amount, 8, '.', '') }}</td>
                                                            <td class="total">
                                                                {{ number_format($order->price * $order->amount, 8, '.', '') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                <tr class="bitcoin-price-row">
                                                    <td colspan="3" class="bitcoin-price">
                                                        <i id="bitcoin-arrow" class="fas fa-arrow-up"></i>
                                                        <span id="bitcoin-price">{{ $BuyPrice }}</span>
                                                    </td>
                                                </tr>

                                                @foreach ($orderBook as $order)
                                                    @if ($order->action == 'buy')
                                                        <!-- Buy orders -->
                                                        <tr class="buy-order">
                                                            <td class="price">
                                                                {{ number_format($order->price, 8, '.', '') }}</td>
                                                            <td class="amount">
                                                                {{ number_format($order->amount, 8, '.', '') }}</td>
                                                            <td class="total">
                                                                {{ number_format($order->price * $order->amount, 8, '.', '') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-5 p-0" style="height:100%;">
                                <div class="col">

                                    <div class="parent">
                                        <div class="EXTRA extra">
                                            {{ $singlePair->name }}
                                        </div>
                                        <div class="logo">
                                            <img src="{{ getImage('assets/images/logoIcon/spotLogo.png') }}"
                                                alt="Logo" class="logo-img">
                                        </div>
                                        <div class="time" style="font-size: 22px;">
                                            Time: <span class="t">{{ $singlePair->fake == 1 ? 1 : 5 }} min</span>
                                        </div>
                                    </div>

                                    <div class="border border-secondary mt-3">
                                        <div id="spotChart" style="height: 500px;"></div>
                                    </div>

                                    <div class="border border-secondary mt-5 pb-2">
                                        <div class="price-info">
                                            <div class="price-col">
                                                <div class="price-label" id="Open_Label">Open</div>
                                                <div class="price-value" id="Open_Value_Label">00</div>
                                            </div>
                                            <div class="price-col">
                                                <div class="price-label" id="Close_Label">Close</div>
                                                <div class="price-value" id="Close_Value_Label">00</div>
                                            </div>
                                            <div class="price-col">
                                                <div class="price-label" id="High_Label">High</div>
                                                <div class="price-value" id="High_Value_Label">00</div>
                                            </div>
                                            <div class="price-col">
                                                <div class="price-label" id="Low_Label">Low</div>
                                                <div class="price-value" id="Low_Value_Label">00</div>
                                            </div>
                                        </div>
                                        <div class="price-col exPrice">Expected Price</div>
                                        <div class="price-col exPrice" id="exPriceValue">0000</div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-lg-3 border border-secondary">
                                <section class="section">
                                    <div class="container">
                                        <div class="tab">
                                            <div class="tab__list">
                                                <div class="tab__item" id="buy">Buy</div>
                                                <div class="tab__item" id="sell">Sell</div>
                                            </div>
                                            <div class="tab__content">
                                                <div class="tab__content-item tab">
                                                    <div class="tab__list">
                                                        <div class="tab__item subItem">Limit</div>
                                                        <div class="tab__item subItem">Market</div>
                                                        <div class="tab__item subItem">Stop-Limit</div>
                                                    </div>
                                                    <div class="tab__content">
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/limit') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start ">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Price
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="BuyLimitPrice"
                                                                                onkeyup="BuyLimitUpdateValuePrice(this)"
                                                                                style="color:#3B926C";
                                                                                value="{{ $BuyPrice }}"
                                                                                name="price" type="number"
                                                                                step="any" min="0" />
                                                                            <script>
                                                                                function BuyLimitUpdateValuePrice(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }

                                                                                    var BLdisplayText = document.getElementById("BLdisplayText");
                                                                                    var Total = value * document.getElementById("BuyLimitAmount").value;
                                                                                    Total = Total.toFixed(8)
                                                                                    BLdisplayText.innerHTML = Total;
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <input type="hidden"
                                                                            value="{{ $pair }}"
                                                                            name="pair" />
                                                                        <input type="hidden" value="buy"
                                                                            name="action" />
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="BuyLimitAmount"
                                                                            onkeyup="BuyLimitUpdateValue(this)"
                                                                            style="color:#3B926C"; type="number"
                                                                            step="any" min="0"
                                                                            value="0" name="amount" />
                                                                        <script>
                                                                            function BuyLimitUpdateValue(input) {
                                                                                let value = input.value;

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    console.log("Not a number", parseFloat(value))
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }

                                                                                var BLdisplayText = document.getElementById("BLdisplayText");
                                                                                var Total = value * document.getElementById("BuyLimitPrice").value;
                                                                                Total = Total.toFixed(8)
                                                                                BLdisplayText.innerHTML = Total;

                                                                                if (Total > 0 && value <= {{ floatval($cryptoBalance2) }})
                                                                                    document.getElementById("BuyLimitBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("BuyLimitBtn").disabled = true;
                                                                            }
                                                                        </script>

                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 p-0">Total Amount</div>
                                                                            <div class="col-6 text-end p-0"><span
                                                                                    id="BLdisplayText">0</span> <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BL25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BL50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BL75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BL100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #3B926C;"
                                                                            id="BuyLimitBtn">
                                                                            {{auth()->check()?"Buy ".$cryptoCurrency1->code : $login }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                @auth
                                                            </form>
                                                            @endauth
                                                        </div>

                                                        <!-- ****************** Market *********************************** -->
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/market') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start ">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 border border-secondary">
                                                                            <div class="col-6">Price</div>
                                                                            <div class="col-6 text-end">
                                                                                Market <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="BuyMarketAmount"
                                                                            onkeyup="BuyMarketUpdateValue(this)"
                                                                            style="color:#3B926C"; type="number"
                                                                            step="any" min="0"
                                                                            value="0" name="amount" />
                                                                        <input type="hidden"
                                                                            value="{{ $pair }}"
                                                                            name="pair" />
                                                                        <input type="hidden" value="buy"
                                                                            name="action" />
                                                                        <script>
                                                                            function BuyMarketUpdateValue(input) {
                                                                                let value = input.value;
                                                                                console.log("value", value)

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    console.log("Not a number", parseFloat(value))
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }
                                                                                if (value > 0 && value <= {{ floatval($cryptoBalance2) }})
                                                                                    document.getElementById("BuyMarketBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("BuyMarketBtn").disabled = true;
                                                                            }
                                                                        </script>
                                                                    </div>

                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BM25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BM50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BM75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="BM100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit" id="BuyMarketBtn"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #3B926C;">
                                                                            {{auth()->check()?"Buy ".$cryptoCurrency1->code : $login}}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <!-- ************** StopLimit ************** -->
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/stoplimit') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start ">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance2 }}
                                                                                {{ $cryptoCurrency2->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Stop
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="BuySLStop"
                                                                                onkeyup="BuySLStopUpdateValue(this)"
                                                                                style="color:#3B926C"; type="number"
                                                                                step="any" min="0"
                                                                                value="{{ $BuyPrice }}"
                                                                                name="stop_price" />
                                                                            <script>
                                                                                function BuySLStopUpdateValue(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Limit
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="BuySLLimit"
                                                                                onkeyup="BuySLLimitUpdateValue(this)"
                                                                                style="color:#3B926C"; type="number"
                                                                                step="any" min="0"
                                                                                value="{{ $BuyPrice }}"
                                                                                name="price" />
                                                                            <script>
                                                                                function BuySLLimitUpdateValue(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }

                                                                                    var SLdisplayText = document.getElementById("SLdisplayText");
                                                                                    var Total = value * document.getElementById("BuySLAmount").value;
                                                                                    Total = Total.toFixed(8)
                                                                                    SLdisplayText.innerHTML = Total;
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="BuySLAmount"
                                                                            onkeyup="BuyStopLimitUpdateValue(this)"
                                                                            style="color:#3B926C"; type="number"
                                                                            step="any" min="0"
                                                                            value="0" name="amount" />
                                                                        <input type="hidden"
                                                                            value="{{ $pair }}"
                                                                            name="pair" />
                                                                        <input type="hidden" value="buy"
                                                                            name="action" />
                                                                        <script>
                                                                            function BuyStopLimitUpdateValue(input) {
                                                                                let value = input.value;

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }

                                                                                var SLdisplayText = document.getElementById("SLdisplayText");
                                                                                var Total = value * document.getElementById("BuySLLimit").value;
                                                                                Total = Total.toFixed(8)
                                                                                SLdisplayText.innerHTML = Total;

                                                                                if (value > 0 && value <= {{ floatval($cryptoBalance2) }})
                                                                                    document.getElementById("BuyStopLimitBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("BuyStopLimitBtn").disabled = true;
                                                                            }
                                                                        </script>

                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 p-0">Total Amount</div>
                                                                            <div class="col-6 text-end p-0"><span
                                                                                    id="SLdisplayText">0</span> <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SL25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SL50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SL75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SL100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit" id="BuyStopLimitBtn"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #3B926C;">
                                                                            {{auth()->check()?"Buy ". $cryptoCurrency1->code:$login }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- __________________________ SELL __________________________ -->

                                                <div class="tab__content-item tab">
                                                    <div class="tab__list">
                                                        <div class="tab__item subItem">Limit</div>
                                                        <div class="tab__item subItem">Market</div>
                                                        <div class="tab__item subItem">Stop-Limit</div>
                                                    </div>
                                                    <div class="tab__content">
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/limit') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Price
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="SellLimitPrice"
                                                                                onkeyup="SellLimitPriceUpdateValue(this)"
                                                                                style="color:#3B926C";
                                                                                value="{{ $BuyPrice }}"
                                                                                name="price" type="number"
                                                                                step="any" min="0" />
                                                                            <script>
                                                                                function SellLimitPriceUpdateValue(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }

                                                                                    var SellLimitdisplayText = document.getElementById("SellLimitdisplayText");
                                                                                    var Total = value * document.getElementById("SellLimitAmount").value;
                                                                                    Total = Total.toFixed(8)
                                                                                    SellLimitdisplayText.innerHTML = Total;
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <input type="hidden"
                                                                            value="{{ $pair }}"
                                                                            name="pair" />
                                                                        <input type="hidden" value="sell"
                                                                            name="action" />
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="SellLimitAmount"
                                                                            onkeyup="SellLimitUpdateValue(this)"
                                                                            style="color:#3B926C"; value="0"
                                                                            name="amount" type="number"
                                                                            step="any" min="0" />
                                                                        <script>
                                                                            function SellLimitUpdateValue(input) {
                                                                                let value = input.value;

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }

                                                                                var SellLimitdisplayText = document.getElementById("SellLimitdisplayText");
                                                                                var Total = value * document.getElementById("SellLimitPrice").value;
                                                                                Total = Total.toFixed(8)
                                                                                SellLimitdisplayText.innerHTML = Total;

                                                                                if (Total > 0 && input.value <= {{ floatval($cryptoBalance1) }})
                                                                                    document.getElementById("SellLimitBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("SellLimitBtn").disabled = true;
                                                                            }
                                                                        </script>

                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 p-0">Total Amount</div>
                                                                            <div class="col-6 text-end p-0"><span
                                                                                    id="SellLimitdisplayText">0</span>
                                                                                <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellLimit25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellLimit50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellLimit75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellLimit100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit" id="SellLimitBtn"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #F44336;;">
                                                                            {{auth()->check()?"Sell ".$cryptoCurrency1->code:$login }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <!-- **************** SELL Market ********************** -->
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/market') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start ">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 border border-secondary">
                                                                            <div class="col-6">Price</div>
                                                                            <div class="col-6 text-end">
                                                                                Market <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="SellMarketAmount"
                                                                            onkeyup="SellMarketUpdateValue(this)"
                                                                            style="color:#3B926C"; value="0"
                                                                            name="amount" type="number"
                                                                            step="any" min="0" />
                                                                        <input type="hidden"
                                                                            value="{{ $pair }}"
                                                                            name="pair">
                                                                        <input type="hidden" value="sell"
                                                                            name="action">
                                                                        <script>
                                                                            function SellMarketUpdateValue(input) {
                                                                                let value = input.value;
                                                                                console.log("value", value)

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    console.log("Not a number", parseFloat(value))
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }

                                                                                if (value <= {{ floatval($cryptoBalance1) }})
                                                                                    document.getElementById("SellMarketBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("SellMarketBtn").disabled = true;
                                                                            }
                                                                        </script>
                                                                    </div>

                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SM25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SM50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SM75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SM100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit" id="SellMarketBtn"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #F44336;">
                                                                            {{auth()->check()?"Sell ".$cryptoCurrency1->code:$login }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <!-- **************** SELL STOP-LIMIT ********************** -->
                                                        <div class="tab__content-item">
                                                        @auth
                                                            <form
                                                                action="{{ url('user/exchange/' . $pair . '/stoplimit') }}"
                                                                method="POST">
                                                                @csrf
                                                                @endauth
                                                                <div class="row justify-content-center">
                                                                    <div>
                                                                        <div class="float-start ">Available</div>
                                                                        {{-- <div class="float-end">Available</div> --}}
                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div>
                                                                            {{-- <div
                                                                                class="col-6 text-end currency_display p-0">
                                                                                {{ $cryptoBalance1 }}
                                                                                {{ $cryptoCurrency1->code }}
                                                                            </div> --}}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Stop
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="SellSLStop"
                                                                                onkeyup="SellSLStopUpdateValue(this)"
                                                                                style="color:#3B926C"; type="number"
                                                                                step="any" min="0"
                                                                                value="{{ $BuyPrice }}"
                                                                                name="stop_price" />
                                                                            <script>
                                                                                function SellSLStopUpdateValue(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="mt-3 row py-1 px-2">
                                                                            <label>Limit
                                                                                ({{ $cryptoCurrency2->code }})</label>
                                                                            <input
                                                                                class="bg-transparent form-control border border-secondary"
                                                                                id="SellSLLimit"
                                                                                onkeyup="SellSLLimitUpdateValue(this)"
                                                                                style="color:#3B926C"; type="number"
                                                                                step="any" min="0"
                                                                                value="{{ $BuyPrice }}"
                                                                                name="price" />
                                                                            <input type="hidden" name="pair"
                                                                                value="{{ $pair }}">
                                                                            <input type="hidden" name="action"
                                                                                value="sell">
                                                                            <script>
                                                                                function SellSLLimitUpdateValue(input) {
                                                                                    let value = input.value;

                                                                                    if (isNaN(parseFloat(value))) {
                                                                                        value = 0;
                                                                                        input.value = "0";
                                                                                    }

                                                                                    var SellSLdisplayText = document.getElementById("SellSLdisplayText");
                                                                                    var Total = value * document.getElementById("SellSLAmount").value;
                                                                                    Total = Total.toFixed(8)
                                                                                    SellSLdisplayText.innerHTML = Total;
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row py-1 px-2">
                                                                        <label>Amount
                                                                            ({{ $cryptoCurrency1->code }})</label>
                                                                        <input
                                                                            class="bg-transparent form-control border border-secondary"
                                                                            id="SellSLAmount"
                                                                            onkeyup="SellStopLimitUpdateValue(this)"
                                                                            style="color:#3B926C"; type="number"
                                                                            step="any" min="0"
                                                                            value="0" name="amount" />
                                                                        <script>
                                                                            function SellStopLimitUpdateValue(input) {
                                                                                let value = input.value;

                                                                                if (isNaN(parseFloat(value))) {
                                                                                    value = 0;
                                                                                    input.value = "0";
                                                                                }

                                                                                var SellSLdisplayText = document.getElementById("SellSLdisplayText");
                                                                                var Total = value * document.getElementById("SellSLLimit").value;
                                                                                Total = Total.toFixed(8)
                                                                                SellSLdisplayText.innerHTML = Total;

                                                                                if (value > 0 && value <= {{ floatval($cryptoBalance1) }})
                                                                                    document.getElementById("BuyStopLimitBtn").disabled = false;
                                                                                else
                                                                                    document.getElementById("BuyStopLimitBtn").disabled = true;
                                                                            }
                                                                        </script>

                                                                    </div>
                                                                    <div>
                                                                        <div
                                                                            class="mt-3 row py-1 px-2 border border-secondary">
                                                                            <div class="col-6 p-0">Total Amount</div>
                                                                            <div class="col-6 text-end p-0"><span
                                                                                    id="SellSLdisplayText">0</span>
                                                                                <span
                                                                                    class="currency_display">{{ $cryptoCurrency2->code }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 row p-0">
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellSL25">
                                                                            25%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellSL50">
                                                                            50%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellSL75">
                                                                            75%
                                                                        </div>
                                                                        <div class="col border border-success py-1 CP"
                                                                            id="SellSL100">
                                                                            100%
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 mb-4 text-white col p-0">
                                                                        <button type="submit" id="SellStopLimitBtn"
                                                                            class="px-4 py-2 rounded-2 w-100"
                                                                            style="background-color: #F44336;">
                                                                            {{auth()->check()?"Sell ".$cryptoCurrency1->code:$login }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="my-5" style="margin-left: -15px; overflow-x:auto;">
                        <div>
                            <table class="table table-borderless table-dark table-hover" id="order"
                                style="overflow-x:auto;">
                                <thead>
                                    <th>Date</th>
                                    <th>Pair</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Filled</th>
                                    <th>Total</th>
                                    <th>Cancel</th>
                                </thead>
                                <tbody class="fw-lighter">
                                    @foreach ($allOrders as $order)
                                        <tr>
                                            <td>{{ $order->created_at }}</td>
                                            <td>{{ $singlePair->name }}</td>
                                            <td>{{ $order->type }}</td>
                                            <td>{{ $order->action }}</td>
                                            <td>{{ $order->status }}</td>
                                            <td>{{ $order->price == null ? '-' : $order->price }}</td>
                                            <td>{{ $order->amount + $order->filled }}</td>
                                            <td>{{ ($order->filled / ($order->amount + $order->filled)) * 100 }} %</td>
                                            <td>{{ $order->price == null || $order->amount == null ? '-' : $order->price * ($order->amount + $order->filled) }}
                                            </td>
                                            @if ($order->status == 'open')
                                                <td>
                                                    <form
                                                        action="{{ url('user/exchange/orders/cancel/' . $order->id . '/' . $order->type) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-outline-success">Cancel</button>
                                                    </form>
                                                </td>
                                            @else
                                                <td style="color: #F44336">{{ $order->status }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div>

                </div>
        </section>
        <!-- ************************************************************ Exchange section ********************************************************************** -->

        @if (!Request::routeIs('user.register') && !Request::routeIs('user.login'))
            @include($activeTemplate . 'partials.footer')
        @endif
    </div>

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp

    @if ($cookie->data_values->status == 1 && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a
                    href="{{ route('cookie.policy') }}" class="text--base" target="_blank">@lang('learn more')</a>
            </p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base w-100 policy">@lang('Allow')</a>
            </div>
        </div>
    @endif

    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
    <script src="https://code.highcharts.com/stock/highstock.js"></script>
    <script src="https://code.highcharts.com/stock/modules/data.js"></script>
    <script src="https://code.highcharts.com/stock/indicators/indicators-all.js"></script>
    <script src="https://code.highcharts.com/stock/modules/drag-panes.js"></script>
    <script src="https://code.highcharts.com/modules/annotations-advanced.js"></script>
    <script src="https://code.highcharts.com/modules/price-indicator.js"></script>
    <script src="https://code.highcharts.com/modules/full-screen.js"></script>
    <script src="https://code.highcharts.com/modules/stock-tools.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
        crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

    <script>
        var $j = jQuery.noConflict();
        $j(document).ready(function() {
            $j('#order').DataTable({
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ]
            });
        });
    </script>

    @stack('script-lib')
    @stack('script')
    @include('partials.plugins')
    @include('partials.notify')

    <script>
        jQuery(".test_info a").click(function() {
            var href = $(this).attr('href');
            console.log(href);
            jQuery('#confirm_modal').modal('show', {
                backdrop: 'static',
                keyboard: false
            });
            $('.agree').attr('href', href);
            return false;
        });
        jQuery(".agree").click(function() {
            return true;
        });
        //
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            $.each($('input, select, textarea'), function(i, element) {
                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').first().addClass('required');
                }
            });

            let headings = $('.table th');
            let rows = $('.table tbody tr');
            let columns
            let dataLabel;
            $.each(rows, function(index, element) {
                columns = element.children;
                if (columns.length == headings.length) {
                    $.each(columns, function(i, td) {
                        dataLabel = headings[i].innerText;
                        $(td).attr('data-label', dataLabel)
                    });
                }
            });
        })(jQuery);
    </script>
    <div class="modal" id="confirm_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notice to buyer</h5>
                    {{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> --}}
                    {{--                    <span aria-hidden="true">&times;</span> --}}
                    {{--                </button> --}}
                </div>
                <div class="modal-body">
                    <p>Please read the seller's terms and conditions carefully before clicking the paid icon and
                        transfer the amount to the seller's account through your banking application. The seller's bank
                        account details can be found in the payment details by going to the up order details. Fraud
                        reporting: If the buyer asks to release the first crypto without paying, please immediately
                        report to BCH INDEX Limited at this email (p2p@bchindex.com)</p>
                </div>
                <div class="modal-footer">
                    {{--                <button type="button" class="btn btn-primary agree">Agree</button> --}}
                    <a class="btn btn-primary agree" href="#">Agree</a>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2-container {
            display: inline-grid !important;
        }
    </style>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/63923141daff0e1306dba38e/1gjph0s6c';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var element = document.body;
            element.dataset.bsTheme = "dark";

            document.getElementById("BuyLimitBtn").disabled = true;
            document.getElementById("BuyMarketBtn").disabled = true;
            document.getElementById("BuyStopLimitBtn").disabled = true;

            document.getElementById("SellLimitBtn").disabled = true;
            document.getElementById("SellMarketBtn").disabled = true;
            document.getElementById("SellStopLimitBtn").disabled = true;

            $('#BuyLimitPrice').val({{ $BuyPrice }});
            $('#BuyLimitAmount').val(0);
            $('#BuyMarketAmount').val(0);
            $('#BuySLStop').val({{ $BuyPrice }});
            $('#BuySLLimit').val({{ $BuyPrice }});
            $('#BuySLAmount').val(0);
            $('#SellLimitPrice').val({{ $BuyPrice }});
            $('#SellLimitAmount').val(0);
            $('#SellMarketAmount').val(0);
            $('#SellSLStop').val({{ $BuyPrice }});
            $('#SellSLLimit').val({{ $BuyPrice }});
            $('#SellSLAmount').val(0);

            // setInterval(displayOrderTable, 5000);
            // generateRandomOrderBookData();

            // TradingView.widget.theme = element.dataset.bsTheme;

            function displayOrderTable() {
                $.ajax({ //kya krr rha
                    url: window.location.href + "/orderBookUpdate",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tableBody = $('#orderBook tbody');

                        $.each(data, function(index, row) {
                            var tableRow = $('<tr>');

                            // Create table cells for each column
                            $('<td>').text(row.price).appendTo(tableRow);
                            $('<td>').text(row.amount).appendTo(tableRow);

                            var total = row.price * row.amount;

                            $('<td>').text(total).appendTo(tableRow);

                            // Append the row to the table body
                            tableBody.append(tableRow);
                        });
                    },
                    error: function() {
                        console.log('Error retrieving table data');
                    }
                });
            }
            // document.getElementById("tradingview_light").style.display = "none";
        }, false);
        // reference from https://www.digitalocean.com/community/tutorials/js-tabs

        const tabs = document.querySelectorAll(".tab");

        function tabify(tab) {
            const tabList = tab.querySelector(".tab__list");

            if (tabList) {
                const tabItems = [...tabList.children];
                const tabContent = tab.querySelector(".tab__content");
                const tabContentItems = [...tabContent.children];
                let tabIndex = 0;

                tabIndex = tabItems.findIndex((item, index) => {
                    return [...item.classList].indexOf("is--active") > -1;
                });

                tabIndex > -1 ? (tabIndex = tabIndex) : (tabIndex = 0);

                function setTab(index) {
                    tabItems.forEach((x, index) => x.classList.remove("is--active"));
                    tabContentItems.forEach((x, index) => x.classList.remove("is--active"));

                    tabItems[index].classList.add("is--active");
                    tabContentItems[index].classList.add("is--active");
                }

                tabItems.forEach((x, index) =>
                    x.addEventListener("click", () => setTab(index))
                );
                setTab(tabIndex);
                tab.querySelectorAll(".tab").forEach((tabContent) => tabify(tabContent));
            }
        }

        tabs.forEach(tabify);
    </script>

    <script>
        $("ul.nav-tabs a").click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>

    <!--End of Tawk.to Script-->
    <script>
        // BUY Limit
        let BL25 = document.getElementById("BL25");
        let BL50 = document.getElementById("BL50");
        let BL75 = document.getElementById("BL75");
        let BL100 = document.getElementById("BL100");
        let BuyLimitAmount = document.getElementById("BuyLimitAmount");

        var BLdisplayText = document.getElementById("BLdisplayText");

        BL25.addEventListener("click", function() {
            BuyLimitAmount.value = {{ ($cryptoBalance2 * 0.25) / $cryptoCurrency1->rate }};
            Total = BuyLimitAmount.value * document.getElementById("BuyLimitPrice").value;
            Total = Total.toFixed(8)
            BLdisplayText.innerHTML = Total;
            if (BuyLimitAmount.value > 0)
                document.getElementById("BuyLimitBtn").disabled = false;
        });
        BL50.addEventListener("click", function() {
            BuyLimitAmount.value = {{ ($cryptoBalance2 * 0.5) / $cryptoCurrency1->rate }};
            Total = BuyLimitAmount.value * document.getElementById("BuyLimitPrice").value;
            Total = Total.toFixed(8)
            BLdisplayText.innerHTML = Total;
            if (BuyLimitAmount.value > 0)
                document.getElementById("BuyLimitBtn").disabled = false;
        });
        BL75.addEventListener("click", function() {
            BuyLimitAmount.value = {{ ($cryptoBalance2 * 0.75) / $cryptoCurrency1->rate }};
            Total = BuyLimitAmount.value * document.getElementById("BuyLimitPrice").value;
            Total = Total.toFixed(8)
            BLdisplayText.innerHTML = Total;
            if (BuyLimitAmount.value > 0)
                document.getElementById("BuyLimitBtn").disabled = false;
        });
        BL100.addEventListener("click", function() {
            BuyLimitAmount.value = {{ $cryptoBalance2 / $cryptoCurrency1->rate }};
            Total = {{ $cryptoBalance2 / $cryptoCurrency1->rate }} * document.getElementById("BuyLimitPrice")
                .value;
            Total = Total.toFixed(8)
            BLdisplayText.innerHTML = Total;
            if (BuyLimitAmount.value > 0)
                document.getElementById("BuyLimitBtn").disabled = false;
        });

        // BUY MARKET
        let BM25 = document.getElementById("BM25");
        let BM50 = document.getElementById("BM50");
        let BM75 = document.getElementById("BM75");
        let BM100 = document.getElementById("BM100");
        let BuyMarketAmount = document.getElementById("BuyMarketAmount");

        BM25.addEventListener("click", function() {
            BuyMarketAmount.value = {{ ($cryptoBalance2 * 0.25) / $cryptoCurrency1->rate }};
            if (BuyMarketAmount.value > 0)
                document.getElementById("BuyMarketBtn").disabled = false;
        });
        BM50.addEventListener("click", function() {
            BuyMarketAmount.value = {{ ($cryptoBalance2 * 0.5) / $cryptoCurrency1->rate }};
            if (BuyMarketAmount.value > 0)
                document.getElementById("BuyMarketBtn").disabled = false;
        });
        BM75.addEventListener("click", function() {
            BuyMarketAmount.value = {{ ($cryptoBalance2 * 0.75) / $cryptoCurrency1->rate }};
            if (BuyMarketAmount.value > 0)
                document.getElementById("BuyMarketBtn").disabled = false;
        });
        BM100.addEventListener("click", function() {
            BuyMarketAmount.value = {{ $cryptoBalance2 / $cryptoCurrency1->rate }};
            if (BuyMarketAmount.value > 0)
                document.getElementById("BuyMarketBtn").disabled = false;
        });

        // BUY Stop-Limit
        let SL25 = document.getElementById("SL25");
        let SL50 = document.getElementById("SL50");
        let SL75 = document.getElementById("SL75");
        let SL100 = document.getElementById("SL100");
        let BuySLAmount = document.getElementById("BuySLAmount");

        var SLdisplayText = document.getElementById("SLdisplayText");

        SL25.addEventListener("click", function() {
            BuySLAmount.value = {{ ($cryptoBalance2 * 0.25) / $cryptoCurrency1->rate }};
            Total = BuySLAmount.value * document.getElementById("BuySLLimit").value;
            Total = Total.toFixed(8)
            SLdisplayText.innerHTML = Total;

            if (BuySLAmount.value > 0)
                document.getElementById("BuyStopLimitBtn").disabled = false;
        });
        SL50.addEventListener("click", function() {
            BuySLAmount.value = {{ ($cryptoBalance2 * 0.5) / $cryptoCurrency1->rate }};
            Total = BuySLAmount.value * document.getElementById("BuySLLimit").value;
            Total = Total.toFixed(8)
            SLdisplayText.innerHTML = Total;

            if (BuySLAmount.value > 0)
                document.getElementById("BuyStopLimitBtn").disabled = false;
        });
        SL75.addEventListener("click", function() {
            BuySLAmount.value = {{ ($cryptoBalance2 * 0.75) / $cryptoCurrency1->rate }};
            Total = BuySLAmount.value * document.getElementById("BuySLLimit").value;
            Total = Total.toFixed(8)
            SLdisplayText.innerHTML = Total;

            if (BuySLAmount.value > 0)
                document.getElementById("BuyStopLimitBtn").disabled = false;
        });
        SL100.addEventListener("click", function() {
            BuySLAmount.value = {{ $cryptoBalance2 / $cryptoCurrency1->rate }};
            Total = BuySLAmount.value * document.getElementById("BuySLLimit").value;
            Total = Total.toFixed(8)
            SLdisplayText.innerHTML = Total;

            if (BuySLAmount.value > 0)
                document.getElementById("BuyStopLimitBtn").disabled = false;
        });

        // --------------------------------- SELL ---------------------------------

        // Limit
        let SellLimit25 = document.getElementById("SellLimit25");
        let SellLimit50 = document.getElementById("SellLimit50");
        let SellLimit75 = document.getElementById("SellLimit75");
        let SellLimit100 = document.getElementById("SellLimit100");

        let SellLimitAmount = document.getElementById("SellLimitAmount");

        var SellLimitdisplayText = document.getElementById("SellLimitdisplayText");

        SellLimit25.addEventListener("click", function() {
            SellLimitAmount.value = {{ ($cryptoBalance1 * 0.25) / $cryptoCurrency2->rate }};
            Total = SellLimitAmount.value * document.getElementById("SellLimitPrice").value;
            Total = Total.toFixed(8)
            SellLimitdisplayText.innerHTML = Total;

            if (SellLimitAmount.value > 0)
                document.getElementById("SellLimitBtn").disabled = false;
        });
        SellLimit50.addEventListener("click", function() {
            SellLimitAmount.value = {{ ($cryptoBalance1 * 0.5) / $cryptoCurrency2->rate }};
            Total = SellLimitAmount.value * document.getElementById("SellLimitPrice").value;
            Total = Total.toFixed(8)
            SellLimitdisplayText.innerHTML = Total;

            if (SellLimitAmount.value > 0)
                document.getElementById("SellLimitBtn").disabled = false;
        });
        SellLimit75.addEventListener("click", function() {
            SellLimitAmount.value = {{ ($cryptoBalance1 * 0.75) / $cryptoCurrency2->rate }};
            Total = SellLimitAmount.value * document.getElementById("SellLimitPrice").value;
            Total = Total.toFixed(8)
            SellLimitdisplayText.innerHTML = Total;

            if (SellLimitAmount.value > 0)
                document.getElementById("SellLimitBtn").disabled = false;
        });
        SellLimit100.addEventListener("click", function() {
            SellLimitAmount.value = {{ $cryptoBalance1 / $cryptoCurrency2->rate }};
            Total = SellLimitAmount.value * document.getElementById("SellLimitPrice").value;
            Total = Total.toFixed(8)
            SellLimitdisplayText.innerHTML = Total;

            if (SellLimitAmount.value > 0)
                document.getElementById("SellLimitBtn").disabled = false;
        });

        // MARKET
        let SM25 = document.getElementById("SM25");
        let SM50 = document.getElementById("SM50");
        let SM75 = document.getElementById("SM75");
        let SM100 = document.getElementById("SM100");
        let SellMarketAmount = document.getElementById("SellMarketAmount");

        SM25.addEventListener("click", function() {
            SellMarketAmount.value = {{ ($cryptoBalance1 * 0.25) / $cryptoCurrency2->rate }};

            if (SellMarketAmount.value > 0)
                document.getElementById("SellMarketBtn").disabled = false;
        });
        SM50.addEventListener("click", function() {
            SellMarketAmount.value = {{ ($cryptoBalance1 * 0.5) / $cryptoCurrency2->rate }};

            if (SellMarketAmount.value > 0)
                document.getElementById("SellMarketBtn").disabled = false;
        });
        SM75.addEventListener("click", function() {
            SellMarketAmount.value = {{ ($cryptoBalance1 * 0.75) / $cryptoCurrency2->rate }};

            if (SellMarketAmount.value > 0)
                document.getElementById("SellMarketBtn").disabled = false;
        });
        SM100.addEventListener("click", function() {
            SellMarketAmount.value = {{ $cryptoBalance1 / $cryptoCurrency2->rate }};

            if (SellMarketAmount.value > 0)
                document.getElementById("SellMarketBtn").disabled = false;
        });

        // Sell Stop-Limit
        let SellSL25 = document.getElementById("SellSL25");
        let SellSL50 = document.getElementById("SellSL50");
        let SellSL75 = document.getElementById("SellSL75");
        let SellSL100 = document.getElementById("SellSL100");
        let SellSLAmount = document.getElementById("SellSLAmount");

        var SellSLdisplayText = document.getElementById("SellSLdisplayText");

        SellSL25.addEventListener("click", function() {
            SellSLAmount.value = {{ ($cryptoBalance1 * 0.25) / $cryptoCurrency2->rate }};
            Total = SellSLAmount.value * document.getElementById("SellSLLimit").value;
            Total = Total.toFixed(8)
            SellSLdisplayText.innerHTML = Total;

            if (SellSLAmount.value > 0)
                document.getElementById("SellStopLimitBtn").disabled = false;
        });
        SellSL50.addEventListener("click", function() {
            SellSLAmount.value = {{ ($cryptoBalance1 * 0.5) / $cryptoCurrency2->rate }};
            Total = SellSLAmount.value * document.getElementById("SellSLLimit").value;
            Total = Total.toFixed(8)
            SellSLdisplayText.innerHTML = Total;

            if (SellSLAmount.value > 0)
                document.getElementById("SellStopLimitBtn").disabled = false;
        });
        SellSL75.addEventListener("click", function() {
            SellSLAmount.value = {{ ($cryptoBalance1 * 0.75) / $cryptoCurrency2->rate }};
            Total = SellSLAmount.value * document.getElementById("SellSLLimit").value;
            Total = Total.toFixed(8)
            SellSLdisplayText.innerHTML = Total;

            if (SellSLAmount.value > 0)
                document.getElementById("SellStopLimitBtn").disabled = false;
        });
        SellSL100.addEventListener("click", function() {
            SellSLAmount.value = {{ $cryptoBalance1 / $cryptoCurrency2->rate }};
            Total = SellSLAmount.value * document.getElementById("SellSLLimit").value;
            Total = Total.toFixed(8)
            SellSLdisplayText.innerHTML = Total;

            if (SellSLAmount.value > 0)
                document.getElementById("SellStopLimitBtn").disabled = false;
        });
    </script>

    <script>
        function myFunction() {
            var element = document.body;
            var s = document.getElementById("s");
            element.dataset.bsTheme =
                element.dataset.bsTheme == "light" ? "dark" : "light";
            $('#order-book').toggleClass('table-dark');
            $('#order').toggleClass('table-dark');

            if (element.dataset.bsTheme == "light") {
                s.classList.toggle('section--bg--light');
                s.classList.toggle('section--bg');
                chart.applyOptions({
                    layout: {
                        backgroundColor: '#ffffff',
                        textColor: '#000',
                    }
                });
            } else if (element.dataset.bsTheme == "dark") {
                s.classList.toggle('section--bg--light');
                s.classList.toggle('section--bg');
                chart.applyOptions({
                    layout: {
                        backgroundColor: '#212529',
                        textColor: '#fff',
                    }
                });
            }

        }
    </script>

    @if ($singlePair->fake == 1)
        <script>
            function format_number(n) {
                if (n < 1) {
                    return n.toFixed(8);
                } else if (n > 1000) {
                    return n.toFixed(2);
                } else {
                    return n.toFixed(4);
                }
            }
            const orders = {
                buy: [],
                sell: []
            };

            function getRandomNumber(min, max) {
                var a = Math.random() * (max - min) + min;
                return a;
            }

            ///////////////////////////////////////// Start Chart /////////////////////////////////////////
            element = document.getElementById('spotChart');

            const chart = LightweightCharts.createChart(element, {
                width: element.width,
                height: element.height,
                layout: {
                    backgroundColor: '#212529',
                    textColor: '#fff',
                    fontFamily: "'Roboto', sans-serif",
                },
                priceScale: {
                    borderColor: '#fff',
                },
                timeScale: {
                    borderColor: '#fff',
                    timeFrame: {
                        seconds: 3
                    },
                    timeVisible: true,
                    secondsVisible: true,
                },
                grid: {
                    vertLines: {
                        visible: true, // hide vertical grid lines
                        color: 'rgba(197, 203, 206, 0.3)',
                    },
                    horzLines: {
                        visible: true, // hide horizontal grid lines
                        color: 'rgba(197, 203, 206, 0.3)',
                    },
                },
            });


            var candleSeries = chart.addCandlestickSeries();

            function addNewCandle() {
                const currentTime = new Date().getTime();
                var candle = {
                    time: currentTime,
                    open: Math.random() * 100,
                    high: Math.random() * 100,
                    low: Math.random() * 100,
                    close: Math.random() * 100
                };
                console.log("candle = ", candle)
                candleSeries.update(candle);
            }

            var chartData = {!! json_encode($chart) !!};

            chartData.forEach(element => {
                element.time = parseInt(element.time)
                element.high = parseFloat(element.high)
                element.low = parseFloat(element.low)
                element.open = parseFloat(element.open)
                element.close = parseFloat(element.close)
            });

            var data = chartData;

            for (var i = 0; i < data.length - 1; i++) {
                candleSeries.update(data[i]);
            }

            var lastCandle = data[data.length - 1];
            candle = data[data.length - 1];
            var count = 0;
            var randomLow = parseInt(getRandomNumber(8, 28));
            var randomHigh = parseInt(getRandomNumber(30, 50));

            const Open_Value_Label = document.getElementById('Open_Value_Label');
            const Close_Value_Label = document.getElementById('Close_Value_Label');
            const High_Value_Label = document.getElementById('High_Value_Label');
            const Low_Value_Label = document.getElementById('Low_Value_Label');
            const exPriceValue = document.getElementById('exPriceValue');

            function mergeTickToBar(price) {
                candle.close = price;
                candle.high = Math.max(candle.high, price);
                candle.low = Math.min(candle.low, price);
                candleSeries.update(candle);

                Open_Value_Label.innerHTML = format_number(candle.open);
                Close_Value_Label.innerHTML = format_number(candle.close);
                High_Value_Label.innerHTML = format_number(candle.high);
                Low_Value_Label.innerHTML = format_number(candle.low);
                const exPrice = (candle.open + candle.close + candle.high + candle.low) / 4;
                exPriceValue.innerHTML = format_number(exPrice);

                if (candle.open > candle.close) {
                    Open_Value_Label.style.color = "#F44336";
                    Close_Value_Label.style.color = "#F44336";
                    Low_Value_Label.style.color = "#F44336";
                    High_Value_Label.style.color = "#F44336";
                    exPriceValue.style.color = "#F44336";
                } else if (candle.open < candle.close) {
                    Open_Value_Label.style.color = "#3B926C";
                    Close_Value_Label.style.color = "#3B926C";
                    Low_Value_Label.style.color = "#3B926C";
                    High_Value_Label.style.color = "#3B926C";
                    exPriceValue.style.color = "#3B926C";
                } else {
                    Open_Value_Label.style.color = "white";
                    Close_Value_Label.style.color = "white";
                    Low_Value_Label.style.color = "white";
                    High_Value_Label.style.color = "white";

                }
            }

            setInterval(() => {
                // Make AJAX GET request to /GetData endpoint
                let GetDataURL = window.location.href + "/GetData/" + data[data.length - 1].time;
                $.ajax({
                    url: GetDataURL,
                    type: "GET",
                    success: function(response) {
                        var chartData = response;
                        //var chartData2 = [...chartData];
                        count = 0;

                        chartData.forEach(element => {
                            element.time = parseInt(element.time)
                            element.high = parseFloat(element.high)
                            element.low = parseFloat(element.low)
                            element.open = parseFloat(element.open)
                            element.close = parseFloat(element.close)
                        });

                        lastCandle = chartData[0];
                        mergeTickToBar(lastCandle.open);

                        data = data.concat([{
                            time: chartData[0].time,
                            open: lastCandle.open,
                            high: lastCandle.open,
                            low: lastCandle.open,
                            close: lastCandle.open
                        }]);


                        candleSeries.update(data[data.length - 1]);

                        candle = data[data.length - 1];
                        console.log("Canlde Ajax: ", candle);

                        randomLow = parseInt(getRandomNumber(8, 28));
                        randomHigh = parseInt(getRandomNumber(30, 50));
                    },
                    error: function(xhr, status, error) {
                        console.error(error); // Log error to console
                    }
                });
            }, 60000);

            setInterval(() => {
                count = count + 1;
                if (count == randomLow) {
                    mergeTickToBar(lastCandle.low);
                } else if (count == randomHigh) {
                    mergeTickToBar(lastCandle.high);
                } else {
                    mergeTickToBar(getRandomNumber(lastCandle.low, lastCandle.high));
                }
                console.log("Last Canlde Haris: ", lastCandle, candle);

            }, 1000);


            candleSeries.applyOptions({
                wickUpColor: '#3B926C',
                upColor: '#3B926C',
                wickDownColor: '#F44336',
                downColor: '#F44336',
                borderVisible: false,
            });

            chart.timeScale().applyOptions({
                borderColor: '#197678',
                barSpacing: 10,
            });

            if ({{ $BuyPrice }} < 1) {
                candleSeries.applyOptions({
                    priceFormat: {
                        precision: 8,
                        minMove: 0.00000001,
                    },
                });
            } else {
                candleSeries.applyOptions({
                    priceFormat: {
                        precision: 2,
                        minMove: 0.01,
                    },
                });
            }

            candleSeries.priceScale().applyOptions({
                borderColor: '#197678',
                autoScale: false, // disables auto scaling based on visible content
                scaleMargins: {
                    top: 0.1,
                    bottom: 0.2,
                },
            });

            chart.applyOptions({
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                    vertLine: {
                        width: 2,
                        color: '#197678',
                        style: LightweightCharts.LineStyle.Solid,
                        labelBackgroundColor: '#197678',
                    },

                    // Horizontal crosshair line (showing Price in Label)
                    horzLine: {
                        color: '#197678',
                        labelBackgroundColor: '#197678',
                    },
                },
            });



            ///////////////////////////////////////// END Chart /////////////////////////////////////////

            function generateOrders() {
                // $BuyPrice = {{ $BuyPrice }};
                $BuyPrice = (candle.close + candle.open + candle.high + candle.low) / 4;

                // for buy
                for (let i = 0; i < 10; i++) {
                    let value;
                    if ($BuyPrice < 1) {
                        value = getRandomNumber(0, $BuyPrice * 0.05);;
                    } else if ($BuyPrice < 99) {
                        value = getRandomNumber(0, 1.0);
                    } else if ($BuyPrice < 999) {
                        value = getRandomNumber(0, 2.0);
                    } else if ($BuyPrice < 9999) {
                        value = getRandomNumber(0, 5.0);
                    } else {
                        value = getRandomNumber(0, 10.0);
                    }

                    let price = $BuyPrice - value;

                    let amount;

                    amount = getRandomNumber(0, 50 / $BuyPrice);

                    orders.buy.push({
                        price: price,
                        amount: amount
                    });
                }

                // for sell
                for (let i = 0; i < 10; i++) {
                    let value;
                    if ($BuyPrice < 1) {
                        value = getRandomNumber(0, $BuyPrice * 0.05);;
                    } else if ($BuyPrice < 99) {
                        value = getRandomNumber(0, 1.0);
                    } else if ($BuyPrice < 999) {
                        value = getRandomNumber(0, 2.0);
                    } else if ($BuyPrice < 9999) {
                        value = getRandomNumber(0, 5.0);
                    } else {
                        value = getRandomNumber(0, 10.0);
                    }

                    let price = $BuyPrice + value;

                    let amount;

                    amount = getRandomNumber(0, 50 / $BuyPrice);

                    orders.sell.push({
                        price: price,
                        amount: amount
                    });
                }

                orders.buy.sort((a, b) => b.price - a.price);
                orders.sell.sort((a, b) => b.price - a.price);

                return orders;
            }


            // Update the order book with new data
            function updateOrderBook(data) {
                //const prevPrice = parseFloat($('#bitcoin-price').text());
                $('#order-book tbody').empty();

                // Populate the sell orders
                for (let i = 0; i < 10; i++) {
                    const order = data.sell[i];
                    let price = parseFloat(order.price);
                    let amount = parseFloat(order.amount);
                    const total = price * amount;

                    if (amount < 1) {
                        amount = amount.toFixed(9);
                    } else if (amount > 1000) {
                        amount = amount.toFixed(2);
                    } else {
                        amount = amount.toFixed(4);
                    }

                    if (price < 1) {
                        price = price.toFixed(9);
                    } else {
                        price = price.toFixed(4);
                    }

                    const row = $('<tr>').addClass('sell-order')
                        .append($('<td>').addClass('price').text(price))
                        .append($('<td>').addClass('amount').text(amount))
                        .append($('<td>').addClass('total').text(total.toFixed(4)));
                    $('#order-book tbody').append(row);
                }

                updateBitcoinPrice();

                // Populate the buy orders
                for (let i = 0; i < 10; i++) {
                    const order = data.buy[i];
                    let price = parseFloat(order.price);
                    let amount = parseFloat(order.amount);
                    const total = price * amount;
                    if (amount < 1) {
                        amount = amount.toFixed(9);
                    } else if (amount > 1000) {
                        amount = amount.toFixed(2);
                    } else {
                        amount = amount.toFixed(4);
                    }

                    if (price < 1) {
                        price = price.toFixed(9);
                    } else {
                        price = price.toFixed(4);
                    }
                    const row = $('<tr>').addClass('buy-order')
                        .append($('<td>').addClass('price').text(price))
                        .append($('<td>').addClass('amount').text(amount))
                        .append($('<td>').addClass('total').text(total.toFixed(4)));
                    $('#order-book tbody').append(row);
                }

            }

            // Update the bitcoin price
            function updateBitcoinPrice() {
                const price = candle.close;
                let priceFinal;

                if (price < 1) {
                    priceFinal = price.toFixed(9);
                } else {
                    priceFinal = price.toFixed(2);
                }

                // $('#bitcoin-price').text(price);
                if (candle.close > candle.open) {
                    const row = $('<tr>').addClass('bitcoin-price-row')
                        .append($('<td>').attr('colspan', 3).addClass('bitcoin-price')
                            .append($('<i>').attr('id', 'bitcoin-arrow').addClass('fas fa-arrow-up'))
                            .append($('<span>').attr('id', 'bitcoin-price').addClass('price-up').text(priceFinal)));
                    $('#order-book tbody').append(row);
                } else {
                    const row = $('<tr>').addClass('bitcoin-price-row')
                        .append($('<td>').attr('colspan', 3).addClass('bitcoin-price')
                            .append($('<i>').attr('id', 'bitcoin-arrow').addClass('fas fa-arrow-down'))
                            .append($('<span>').attr('id', 'bitcoin-price').addClass('price-down').text(priceFinal)));
                    $('#order-book tbody').append(row);
                }
            }

            // Update the order book and bitcoin price every 3 seconds
            setInterval(() => {
                const OrderBookData = generateOrders();
                updateOrderBook(OrderBookData);

                if (orders.buy.length > 15) {
                    let number = getRandomNumber(4, 12);
                    orders.buy.sort((a, b) => b - a);
                    orders.buy.splice(0, number)
                }
                if (orders.sell.length > 15) {
                    let number = getRandomNumber(4, 12);
                    orders.sell.sort((a, b) => a - b);
                    orders.sell.splice(0, number)
                }

            }, 500);

            // Initial update
            const OrderBookData = generateOrders();
            updateOrderBook(OrderBookData);
        </script>
    @else
        <script>
            function format_number(n) {
                if (n < 1) {
                    return n.toFixed(8);
                } else if (n > 1000) {
                    return n.toFixed(2);
                } else {
                    return n.toFixed(4);
                }
            }
            const orders = {
                buy: [],
                sell: []
            };

            function getRandomNumber(min, max) {
                var a = Math.random() * (max - min) + min;
                return a;
            }

            ///////////////////////////////////////// Start Chart /////////////////////////////////////////
            element = document.getElementById('spotChart');

            const chart = LightweightCharts.createChart(element, {
                width: element.width,
                height: element.height,
                layout: {
                    backgroundColor: '#212529',
                    textColor: '#fff',
                    fontFamily: "'Roboto', sans-serif",
                },
                priceScale: {
                    borderColor: '#fff',
                },
                timeScale: {
                    borderColor: '#fff',
                    timeFrame: {
                        seconds: 3
                    },
                    timeVisible: true,
                    secondsVisible: true,
                },
                grid: {
                    vertLines: {
                        visible: true, // hide vertical grid lines
                        color: 'rgba(197, 203, 206, 0.3)',
                    },
                    horzLines: {
                        visible: true, // hide horizontal grid lines
                        color: 'rgba(197, 203, 206, 0.3)',
                    },
                },
            });


            var candleSeries = chart.addCandlestickSeries();

            function addNewCandle() {
                const currentTime = new Date().getTime();
                var candle = {
                    time: currentTime,
                    open: Math.random() * 100,
                    high: Math.random() * 100,
                    low: Math.random() * 100,
                    close: Math.random() * 100
                };
                console.log("candle = ", candle)
                candleSeries.update(candle);
            }

            var chartData = {!! json_encode($chart) !!};
            console.log("chartData = ", chartData);

            chartData.forEach(element => {
                element.time = parseInt(element.time)
                element.high = parseFloat(element.high)
                element.low = parseFloat(element.low)
                element.open = parseFloat(element.open)
                element.close = parseFloat(element.close)
            });

            var data = chartData;

            for (var i = 0; i < data.length - 1; i++) {
                candleSeries.update(data[i]);
            }

            var lastCandle = data[data.length - 1];
            candle = data[data.length - 1];
            var count = 0;
            var randomLow = parseInt(getRandomNumber(8, 28));
            var randomHigh = parseInt(getRandomNumber(30, 50));

            const Open_Value_Label = document.getElementById('Open_Value_Label');
            const Close_Value_Label = document.getElementById('Close_Value_Label');
            const High_Value_Label = document.getElementById('High_Value_Label');
            const Low_Value_Label = document.getElementById('Low_Value_Label');
            const exPriceValue = document.getElementById('exPriceValue');

            function mergeTickToBar(price) {
                candle.close = price;
                candle.high = Math.max(candle.high, price);
                candle.low = Math.min(candle.low, price);
                candleSeries.update(candle);

                Open_Value_Label.innerHTML = format_number(candle.open);
                Close_Value_Label.innerHTML = format_number(candle.close);
                High_Value_Label.innerHTML = format_number(candle.high);
                Low_Value_Label.innerHTML = format_number(candle.low);
                const exPrice = (candle.open + candle.close + candle.high + candle.low) / 4;
                exPriceValue.innerHTML = format_number(exPrice);

                if (candle.open > candle.close) {
                    Open_Value_Label.style.color = "#F44336";
                    Close_Value_Label.style.color = "#F44336";
                    Low_Value_Label.style.color = "#F44336";
                    High_Value_Label.style.color = "#F44336";
                    exPriceValue.style.color = "#F44336";
                } else if (candle.open < candle.close) {
                    Open_Value_Label.style.color = "#3B926C";
                    Close_Value_Label.style.color = "#3B926C";
                    Low_Value_Label.style.color = "#3B926C";
                    High_Value_Label.style.color = "#3B926C";
                    exPriceValue.style.color = "#3B926C";
                } else {
                    Open_Value_Label.style.color = "white";
                    Close_Value_Label.style.color = "white";
                    Low_Value_Label.style.color = "white";
                    High_Value_Label.style.color = "white";

                }

            }

            setInterval(() => {
                // Make AJAX GET request to /GetData endpoint
                let GetDataURL = window.location.href + "/GetData/" + data[data.length - 1].time;
                $.ajax({
                    url: GetDataURL,
                    type: "GET",
                    success: function(response) {
                        var chartData = response;
                        //var chartData2 = [...chartData];
                        count = 0;

                        chartData.forEach(element => {
                            element.time = parseInt(element.time)
                            element.high = parseFloat(element.high)
                            element.low = parseFloat(element.low)
                            element.open = parseFloat(element.open)
                            element.close = parseFloat(element.close)
                        });

                        lastCandle = chartData[0];
                        mergeTickToBar(lastCandle.open);

                        data = data.concat([{
                            time: chartData[0].time,
                            open: lastCandle.open,
                            high: lastCandle.open,
                            low: lastCandle.open,
                            close: lastCandle.open
                        }]);


                        candleSeries.update(data[data.length - 1]);

                        candle = data[data.length - 1];
                        console.log("Candle Ajax: ", candle);

                        randomLow = lastCandle.low;
                        randomHigh = lastCandle.high;
                    },
                    error: function(xhr, status, error) {
                        console.error(error); // Log error to console
                    }
                });
            }, 300000);

            setInterval(() => {
                count = count + 1;
                if (count == randomLow) {
                    mergeTickToBar(lastCandle.low);
                } else if (count == randomHigh) {
                    mergeTickToBar(lastCandle.high);
                } else {
                    mergeTickToBar(getRandomNumber(lastCandle.low, lastCandle.high));
                }
                console.log("Last Canlde: ", lastCandle, candle);

            }, 1000);


            candleSeries.applyOptions({
                wickUpColor: '#3B926C',
                upColor: '#3B926C',
                wickDownColor: '#F44336',
                downColor: '#F44336',
                borderVisible: false,
            });

            chart.timeScale().applyOptions({
                borderColor: '#197678',
                barSpacing: 10,
            });

            if ({{ $BuyPrice }} < 1) {
                candleSeries.applyOptions({
                    priceFormat: {
                        precision: 8,
                        minMove: 0.00000001,
                    },
                });
            } else {
                candleSeries.applyOptions({
                    priceFormat: {
                        precision: 2,
                        minMove: 0.01,
                    },
                });
            }

            candleSeries.priceScale().applyOptions({
                borderColor: '#197678',
                autoScale: false, // disables auto scaling based on visible content
                scaleMargins: {
                    top: 0.1,
                    bottom: 0.2,
                },
            });

            chart.applyOptions({
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                    vertLine: {
                        width: 2,
                        color: '#197678',
                        style: LightweightCharts.LineStyle.Solid,
                        labelBackgroundColor: '#197678',
                    },

                    // Horizontal crosshair line (showing Price in Label)
                    horzLine: {
                        color: '#197678',
                        labelBackgroundColor: '#197678',
                    },
                },
            });



            ///////////////////////////////////////// END Chart /////////////////////////////////////////

            function generateOrders() {
                // $BuyPrice = {{ $BuyPrice }};
                $BuyPrice = (candle.close + candle.open + candle.high + candle.low) / 4;

                // for buy
                for (let i = 0; i < 10; i++) {
                    let value;
                    if ($BuyPrice < 1) {
                        value = getRandomNumber(0, $BuyPrice * 0.05);;
                    } else if ($BuyPrice < 99) {
                        value = getRandomNumber(0, 1.0);
                    } else if ($BuyPrice < 999) {
                        value = getRandomNumber(0, 2.0);
                    } else if ($BuyPrice < 9999) {
                        value = getRandomNumber(0, 5.0);
                    } else {
                        value = getRandomNumber(0, 10.0);
                    }

                    let price = $BuyPrice - value;

                    let amount;

                    amount = getRandomNumber(0, 50 / $BuyPrice);

                    orders.buy.push({
                        price: price,
                        amount: amount
                    });
                }

                // for sell
                for (let i = 0; i < 10; i++) {
                    let value;
                    if ($BuyPrice < 1) {
                        value = getRandomNumber(0, $BuyPrice * 0.05);;
                    } else if ($BuyPrice < 99) {
                        value = getRandomNumber(0, 1.0);
                    } else if ($BuyPrice < 999) {
                        value = getRandomNumber(0, 2.0);
                    } else if ($BuyPrice < 9999) {
                        value = getRandomNumber(0, 5.0);
                    } else {
                        value = getRandomNumber(0, 10.0);
                    }

                    let price = $BuyPrice + value;

                    let amount;

                    amount = getRandomNumber(0, 50 / $BuyPrice);

                    orders.sell.push({
                        price: price,
                        amount: amount
                    });
                }

                orders.buy.sort((a, b) => b.price - a.price);
                orders.sell.sort((a, b) => b.price - a.price);

                return orders;
            }

            function updateOrderBook() {
                let updateOBD = window.location.href + "/orderBookUpdate/"; // Replace with the pair you want to fetch data for

                $.ajax({
                    url: updateOBD,
                    method: 'GET',
                    success: function(data) {
                        console.log(data);
                        // Clear the order book table
                        $('#order-book tbody').empty();

                        for (let i = 0; i < data.length; i++) {
                            if (data[i].action == 'sell') {
                                let price = parseFloat(data[i].price);
                                let amount = parseFloat(data[i].amount);
                                const total = price * amount;

                                if (amount < 1) {
                                    amount = amount.toFixed(9);
                                } else if (amount > 1000) {
                                    amount = amount.toFixed(2);
                                } else {
                                    amount = amount.toFixed(4);
                                }

                                if (price < 1) {
                                    price = price.toFixed(9);
                                } else {
                                    price = price.toFixed(4);
                                }

                                const row = $('<tr>').addClass('sell-order')
                                    .append($('<td>').addClass('price').text(price))
                                    .append($('<td>').addClass('amount').text(amount))
                                    .append($('<td>').addClass('total').text(total.toFixed(4)));
                                $('#order-book tbody').append(row);
                            }
                        }

                        updateBitcoinPrice();

                        for (let i = 0; i < data.length; i++) {
                            if (data[i].action == 'buy') {
                                let price = parseFloat(data[i].price);
                                let amount = parseFloat(data[i].amount);
                                const total = price * amount;

                                if (amount < 1) {
                                    amount = amount.toFixed(9);
                                } else if (amount > 1000) {
                                    amount = amount.toFixed(2);
                                } else {
                                    amount = amount.toFixed(4);
                                }

                                if (price < 1) {
                                    price = price.toFixed(9);
                                } else {
                                    price = price.toFixed(4);
                                }

                                const row = $('<tr>').addClass('buy-order')
                                    .append($('<td>').addClass('price').text(price))
                                    .append($('<td>').addClass('amount').text(amount))
                                    .append($('<td>').addClass('total').text(total.toFixed(2)));

                                $('#order-book tbody').append(row);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error fetching order book data');
                    }
                });
            }

            // Update the bitcoin price
            function updateBitcoinPrice() {
                const price = candle.close;
                let priceFinal;

                if (price < 1) {
                    priceFinal = price.toFixed(9);
                } else {
                    priceFinal = price.toFixed(2);
                }

                // $('#bitcoin-price').text(price);
                if (candle.close > candle.open) {
                    const row = $('<tr>').addClass('bitcoin-price-row')
                        .append($('<td>').attr('colspan', 3).addClass('bitcoin-price')
                            .append($('<i>').attr('id', 'bitcoin-arrow').addClass('fas fa-arrow-up'))
                            .append($('<span>').attr('id', 'bitcoin-price').addClass('price-up').text(priceFinal)));
                    $('#order-book tbody').append(row);
                } else {
                    const row = $('<tr>').addClass('bitcoin-price-row')
                        .append($('<td>').attr('colspan', 3).addClass('bitcoin-price')
                            .append($('<i>').attr('id', 'bitcoin-arrow').addClass('fas fa-arrow-down'))
                            .append($('<span>').attr('id', 'bitcoin-price').addClass('price-down').text(priceFinal)));
                    $('#order-book tbody').append(row);
                }
            }

            // Update the order book and bitcoin price every 3 seconds
            setInterval(() => {
                const OrderBookData = generateOrders();
                updateOrderBook(OrderBookData);

                if (orders.buy.length > 15) {
                    let number = getRandomNumber(4, 12);
                    orders.buy.sort((a, b) => b - a);
                    orders.buy.splice(0, number)
                }
                if (orders.sell.length > 15) {
                    let number = getRandomNumber(4, 12);
                    orders.sell.sort((a, b) => a - b);
                    orders.sell.splice(0, number)
                }

            }, 1000);

            // Initial update
            const OrderBookData = generateOrders();
            updateOrderBook(OrderBookData);
        </script>
    @endif

</body>

</html>
