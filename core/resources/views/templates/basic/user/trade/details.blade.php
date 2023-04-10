@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $profileImage = fileManager()->userProfile();
        $user = auth()->user();
        $topImage = $trade->buyer_id == $user->id ? @$trade->seller->image : @$trade->buyer->image;
        $authBuyer = $user->id == $trade->buyer_id;

        $lastTime = Carbon\Carbon::parse($trade->paid_at)->addMinutes($trade->window);
        $remainingMin = $lastTime->diffInMinutes(now());

        $endTime = $trade->created_at->addMinutes($trade->window);
        $remainingMinitues = $endTime->diffInMinutes(now());
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
    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 text-center mb-4">
                    <h3 class="mb-1">{{ $title }}</h3>
                    <h6 class="text--base">{{ $title2 }}</h6>
                </div>

                <div class="col-lg-6 pl-lg-5 mt-lg-0 mt-5">
                    @include($activeTemplate . 'user.trade.partials.chat_box')
                </div>

                <div class="col-lg-6 mt-lg-0 mt-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            
                            <span class="fw-bold text-sm text-muted">
                                <span>#{{ $trade->uid }}</span>
                            </span>
                            <span>
                                @php echo $trade->statusBadge @endphp
                            </span>
                            <div style="float: right; display:none">
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
                        </div>
                        
                        <div class="card-body">
                            @include($activeTemplate . 'user.trade.partials.alerts')
                            
                            <div id="actions">
                            @include($activeTemplate . 'user.trade.partials.actions')
                            </div>
                            @include($activeTemplate . 'user.trade.partials.info')
                            @include($activeTemplate . 'user.trade.partials.instructions')
                        </div>
                    </div>
                </div>

                @include($activeTemplate . 'user.trade.partials.review')

                 @if ($trade->reviewed == 1 && $trade->advertisement->user_id != auth()->id())
                    <div class="mt-5 alert alert-warning">
                        @lang('You\'ve already given feedback on this advertisement.') <a href="{{ route('user.trade.request.new', $trade->advertisement->id) }}" class="text--base">@lang('View Reviews')</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            
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
            

            setInterval(get_chat, 5000);
            
            function get_chat(){
                $.ajax({
                    type: "GET",
                    url: "{{route('user.trade.request.ajax_chat',$trade->uid)}}",
                    success: function (feedback) {
                        //console.log(feedback);
                        $('div.chat-box__thread').html(feedback);
                    }
                });
            }
            
            myInterval = setInterval(get_actions, 5500);
            
            function get_actions(){
                $.ajax({
                    type: "GET",
                    url: "{{route('user.trade.request.get_ajax_actions',$trade->uid)}}",
                    success: function (feedback) {
                        //console.log(feedback);
                        $('div#actions').html(feedback);
                    }
                });
            }
            
            clearInterval(myInterval);
            
            $("form#chat_update").submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $('#send_chat').html('Sending...');
                $.ajax({
                    url: '{{ route('user.chat.store', $trade->id) }}',
                    type: 'POST',
                    data: formData,
                    success: function (data) {
                        document.getElementById("chat-message-field").value = "";
                        document.getElementById("file").value = "";
                        $('#send_chat').html('Send');
                        get_chat();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });

            function startTimer(duration, display) {
                let timer = duration;
                let minutes;
                let seconds;
                if (display) {
                    setInterval(function() {
                        minutes = parseInt(timer / 60, 10);
                        seconds = parseInt(timer % 60, 10);

                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;
                        display.textContent = minutes + ":" + seconds;

                        if (--timer < 0) {
                            timer = duration;
                        }
                    }, 1000);
                }

            }

            @if ($trade->status == 0)
                window.onload = function() {
                    let cancelMinutes = 60 * '{{ $remainingMinitues }}';
                    let display = document.querySelector('#cancel-min');
                    startTimer(cancelMinutes, display);
                };
            @endif

            @if ($trade->status == 2)
                window.onload = function() {
                    var disputeMinutes = 60 * '{{ $remainingMin }}';
                    let display = document.querySelector('#dispute-min');
                    startTimer(disputeMinutes, display);
                };
            @endif

        })(jQuery);
    </script>
@endpush
