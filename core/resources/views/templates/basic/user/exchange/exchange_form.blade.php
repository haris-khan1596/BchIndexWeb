@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container  pt-120 pb-120">
        <div class="row justify-content-center gy-4">
            <div class="col-lg-8">
                <form action="" method="POST" id="form">
                    @csrf
                    <div class="d-widget">
                        <div class="d-widget__content px-5">
                            <div class="p-4 border mb-4">
                                <div class="row">
                                    <div class="col-lg-12 form-group">
                                        <label class="mb-0">@lang('Amount') </label>
                                        <input type="number" class="form-control style--two amount" name="amount"
                                               placeholder="0.00" required value="{{old('amount')}}">
                                    </div>
                                </div><!-- row end -->
                            </div>

                            <div class="p-4 border mb-4">
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label class="mb-0">@lang('From Currency') </label>
                                        <select class="select style--two from_currency" name="from_wallet_id" required>
                                            <option value="">--@lang('From Currency')--</option>
                                            @foreach (auth()->user()->wallets()->where('balance','>',0)->get() as $fromWallet)
                                                <option value="{{$fromWallet->id}}"
                                                        data-code="{{$fromWallet->currency->code}}"
                                                        data-rate="{{$fromWallet->currency->rate}}"
                                                        data-type="2">{{$fromWallet->currency->code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label class="mb-0">@lang('To Currency') </label>
                                        <select class="select style--two to_currency" name="to_wallet_id" required>
                                            <option value="">--@lang('To Currency')--</option>
                                            @foreach (auth()->user()->wallets()->get() as $toWallet)
                                                <option value="{{$toWallet->id}}"
                                                        data-code="{{$toWallet->currency->code}}"
                                                        data-rate="{{$toWallet->currency->rate}}"
                                                        data-type="2">{{$toWallet->currency->code}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div><!-- row end -->
                            </div>

                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-md btn--base mt-4 exchange">@lang('Exchange')</button>
                    </div>

                    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered " role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">@lang('Exchange Calculation')</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-0">
                                    <div class="d-widget border-start-0 shadow-sm">
                                        <div class="d-widget__content">
                                            <ul class="cmn-list-two text-center mt-4">
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <strong class="from_curr"> </strong>
                                                    <strong class="text--base">@lang('TO')</strong>
                                                    <strong class="to_curr"></strong>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span class="from_curr_val"></span>
                                                    <strong>---------------------------------------------------</strong>
                                                    <span class="to_curr_val"></span>
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="d-widget__footer text-center border-0 pt-3">
                                            <button type="submit"
                                                    class="btn btn-md w-100 d-block btn--base req_confirm">@lang('Confirm')
                                                <i class="las la-long-arrow-alt-right"></i></button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function ($) {
            $('.to_currency').on('change', function () {
                var fromCurr = $('.from_currency option:selected').val()
                if ($('.to_currency option:selected').val() == fromCurr) {
                    notify('error', 'Can\'t exchange within same wallet.')
                    $('.exchange').attr('disabled', true);
                } else {
                    $('.exchange').attr('disabled', false);
                }

            })

            $('.exchange').on('click', function () {
                var amount = $('.amount').val()
                if (amount == '') {
                    notify('error', 'Please provide the amount first.')
                    return false
                }
                var fromCurr = $('.from_currency option:selected').data('code')
                var toCurr = $('.to_currency option:selected').data('code')
                if (!fromCurr || !toCurr) {
                    notify('error', 'Please select the currencies.')
                    return false
                }
                var toCurrType = $('.to_currency option:selected').data('type')

                var fromCurrRate = parseFloat($('.from_currency option:selected').data('rate'))

                var baseCurrAmount = amount;
                var toCurrRate = parseFloat($('.to_currency option:selected').data('rate'))

                var siteCurRate = 1 / fromCurrRate;
                var myCurRate = siteCurRate * toCurrRate;

                console.log('fromCurrRate '+fromCurrRate);

                console.log('toCurrRate '+toCurrRate);

                console.log('siteCurRate '+siteCurRate);

                var toCurrAmount;
                if (toCurrType == 1) {
                    toCurrAmount = (baseCurrAmount * myCurRate).toFixed(2)
                } else {
                    toCurrAmount = (baseCurrAmount * myCurRate).toFixed(6)
                }

                console.log('myCurRate'+myCurRate);

                console.log('toCurrAmount'+toCurrAmount);

                //return false;

                $('#confirm').find('.from_curr').text(fromCurr)
                $('#confirm').find('.to_curr').text(toCurr)
                $('#confirm').find('.from_curr_val').text(parseFloat(amount))
                $('#confirm').find('.to_curr_val').text(toCurrAmount)
                $('#confirm').modal('show')
            })

            $('.req_confirm').on('click', function () {
                $('#form').submit()
                $(this).attr('disabled', true)
            })
        })(jQuery);
    </script>
@endpush

