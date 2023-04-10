@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container  pt-120 pb-120">
        <div class="row justify-content-center gy-4">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="contact-form-wrapper">
                    <form class="contact-form verify-gcaptcha" action="{{ route('user.transfer-money-post') }}"
                          method="POST">
                        @csrf
                        <input type="hidden" name="otherUser" id="otherUser" required value="{{old('otherUser')}}">
                        <div class="row align-items-center">
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label class="mb-0">@lang('Your Wallet')</label>
                                    <select class="select style--two currency" name="wallet_id" id="wallet_id" required
                                            onchange="yourWalletChanged(this)">
                                        <option value="" selected>@lang('Select Wallet')</option>
                                        @foreach ($wallets as $wallet)
                                            <option value="{{ $wallet->id }}"
                                                    data-code="{{ __($wallet->crypto->code) }}">{{ __($wallet->crypto->code) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="container" style="margin-top: -16px;">
                                        <small style="color:#fca120" id="your-wallet-own-amount"></small>
                                        <small style="color:#031737;float:right;margin-top:2px"
                                               id="your-wallet-usd-amount"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="mb-0">@lang('Amount to transfer')</label>
                                    <input name="amount" id="amount" type="text" oninput="amountChanged(this)"
                                           placeholder="@lang('0.00')" class="form-control" value="{{ old('amount') }}"
                                           required>
                                </div>
                                <div class="container" style="margin-top: -16px;">
                                    <small style="color:#fca120" id="usd-amount-in-wallet"></small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group" style="margin-top: 16px;">
                                    <label class="mb-0">@lang('Receiver Username / E-mail')</label>
                                    <input name="user" id="username" type="text"
                                           onchange="receiverUsernameChanged(this)"
                                           placeholder="@lang('Receiver Username / E-mail')" class="form-control"
                                           required>
                                </div>
                            </div>
                            <small class="text-danger" style="padding-left:52%;margin-top:-10px"
                                   id="user-issue-small"></small>

                            <div class="col-lg-12">
                                <button type="submit" id="btn_tr" class="btn--base w-100">@lang('Transfer Now')</button>
                            </div>
                        </div>
                    </form>
                </div><!-- contact-form-wrapper end -->
            </div>


        </div>
    </div>
    <div class="modal" id="receipt_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Loading...</p>
                </div>
                <div class="modal-footer"><a class="close" href="#">Download Receipt</a></div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let transactions_id = @json($transactions_id);
        console.log(transactions_id);
        if (transactions_id != '') {
            get_transactions_popup();
        }

        function get_transactions_popup() {
            $.ajax({
                url: "{{ route('user.transfer-user-receipt',[$transactions_id,true]) }}",
                method: "get",
                success: res => {
                    if (res != '') {
                        //$('.modal-content').css('width', '600px');
                        $('#receipt_modal .modal-body').html(res);
                        $('#p2p_receipt').css('width', '100%');
                        $('#receipt_modal').modal('show');
                    }
                }
            })
        }

        $('.select').select2();

        $('.close').on('click', function (){
            window.open('{{route('user.transfer-user-receipt',[$transactions_id,false])}}', '_blank');
            window.location.replace('{{route('user.transfer')}}');
        });

        window.walletsAmounts = @json($walletsAmounts);

        function receiverUsernameChanged(element) {
            $('#otherUser').val('');
            $('#btn_tr').attr('disabled', 'disabled');
            let userName = $(element).val();
            $.ajax({
                url: "{{ route('user.transfer-user-check') }}",
                method: "get",
                data: {userName},
                success: res => {
                    if (res.success) {
                        $('#user-issue-small').text('')
                        $('#otherUser').val(res.user.id);
                        $('#btn_tr').removeAttr('disabled');
                    } else {
                        $('#user-issue-small').text(res.msg);
                    }
                }
            })
        }

        function yourWalletChanged(element) {
            let selectedWalletID = $(element).val();
            if (selectedWalletID.length == 0) {
                $('#your-wallet-own-amount').text('');
                $('#your-wallet-usd-amount').text('');
                amountChanged($('#amount'))
                return;
            }
            let selecteWalletAmount = window.walletsAmounts.find(walletAmount => walletAmount.id == selectedWalletID)
            $('#your-wallet-own-amount').text(selecteWalletAmount.own_balance);
            $('#your-wallet-usd-amount').text(selecteWalletAmount.usd_balance);
            amountChanged($('#amount'))
        }

        function amountChanged(element) {
            let selectedWalletID = $('#wallet_id').val();
            let amountEntered = $(element).val();
            if (amountEntered.length > 0 && selectedWalletID.length > 0) {
                let selecteWalletAmount = window.walletsAmounts.find(walletAmount => walletAmount.id == selectedWalletID)
                usdAmountInWallet = (1 / Number(selecteWalletAmount.rate)) * Number(amountEntered);
                let n = usdAmountInWallet.toFixed(6);
                $('#usd-amount-in-wallet').text(n + ' ' + selecteWalletAmount.code);
            } else {
                $('#usd-amount-in-wallet').text('')
            }
        }
    </script>
@endpush
