@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php $cryptoImage = fileManager()->crypto(); @endphp
    <section class="pt-60 pb-60 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="border-bottom-0 text-center row">
                        <div class="col-lg-4">
                            <label>
                                Search By Wallets
                            </label>
                        </div>
                        <div class="col-lg-6">
                            <select class="select" id="wallets_id" name="wallets_id">
                                <option value="">Search Wallets</option>
                                @foreach ($wallets as $wallet)
                                    <option @if (request()->id == $wallet->crypto->id) selected @endif
                                        value="{{ $wallet->crypto->id . '|' . $wallet->crypto->code }}">
                                        {{ $wallet->crypto->code }}
                                        <?php
                                        if (!empty($cryptoWallets) && request()->id == $wallet->crypto->id) {
                                            echo '(' . $cryptoWallets->where('crypto_id', $wallet->crypto_id)->count() . ')';
                                        }
                                        if ($wallet->crypto->rate == 0) {
                                            $wallet->crypto->rate = 1;
                                        }
                                        ?>
                                        {{ showAmount($wallet->balance / $wallet->crypto->rate, 6) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <ul style="display: none" class="btn-list justify-content-center mb-4">
                            <li><a href="{{ route('user.wallets') }}"
                                    class="btn btn-sm btn-outline--base @if (!request()->id) active @endif">@lang('All')</a>
                            </li>
                            @foreach ($wallets as $wallet)
                                <li>
                                    <a href="{{ route('user.wallets.single', [$wallet->crypto->id, $wallet->crypto->code]) }}"
                                        class="btn btn-sm btn-outline--base @if (request()->id == $wallet->crypto->id) active @endif"><span>{{ $wallet->crypto->code }}</span>
                                        <?php
                                        if (!empty($cryptoWallets) && request()->id == $wallet->crypto->id) {
                                            echo '(' . $cryptoWallets->where('crypto_id', $wallet->crypto_id)->count() . ')';
                                        }
                                        ?>
                                        {{ showAmount($wallet->balance / $wallet->crypto->rate, 6) }}</a>
                                </li>
                            @endforeach
                        </ul>

                        @foreach ($wallets as $wallet)
                            @if (Request::routeIs('user.wallets.single'))
                                @if ($crypto->id == $wallet->crypto->id)
                                    <div class="text-center mt-4">
                                        <h4>@lang('Deposit Charge is') @if ($wallet->crypto->dc_fixed > 0)
                                                {{ $wallet->crypto->dc_fixed }} {{ $wallet->crypto->code }} +
                                            @endif {{ $wallet->crypto->dc_percent }}%
                                        </h4>
                                    </div>

                                    <div class="mt-2 d-flex flex-wrap justify-content-center">
                                        <a href="{{ route('user.wallets.generate', $wallet->crypto->code) }}"
                                            class="confirm link-btn m-2"><i class="las la-plus"></i> @lang('Generate New')
                                            {{ $wallet->crypto->code }} @lang('Address')
                                        </a>

                                        <a href="{{ route('user.withdraw', $wallet->crypto->code) }}"
                                            class="confirm link-btn m-2"><i class="las la-credit-card"></i>
                                            @lang('Withdraw') {{ $wallet->crypto->code }}
                                        </a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center" style="overflow-x:auto;">
                        {{-- <a href="{{ url('/user/transfer_p2p') }}">
                            <button style="background-color: #3B926C; color: white; border: 1px solid #3B926C"
                                class="btn btn-primary mt-5" id="transfer-btn">Transfer</button>
                        </a> --}}

                        <table id="assets-table" class="table table-borderless table-hover mt-5">
                            <thead>
                                <tr style="background-color: #F9F9F9">
                                    <th>Asset</th>
                                    <th>Symbol</th>
                                    <th>Amount</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through your data and populate the table rows -->
                                @php
                                    $count = 0;
                                @endphp
                                @foreach ($wallets as $wallet)
                                    @if ($wallet->balance > 0)
                                        <tr style="background-color: #F9F9F9">
                                            <td>{{ $wallet->crypto->name }}</td>
                                            <td><img style="width: 30px; height: 30px;" src="{{ getImage($cryptoImage->path . '/' . $wallet->crypto->image, $cryptoImage->size) }}" alt="@lang('image')"></td>
                                            <td>{{ number_format((float) ($wallet->balance / $wallet->crypto->rate), 10, '.', '') }}
                                            </td>
                                            <td>${{ $wallet->balance }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ url('user/' . $wallet->id . '/transfer_p2p') }}"
                                                        class="mx-3" data-toggle="tooltip" data-placement="top"
                                                        title="Transfer to Spot"><img
                                                            src="{{ getImage('assets/images/icons/transferTo.png') }}"
                                                            alt="transfer"></a>
                                                </div>
                                            </td>
                                        </tr>
                                    
                                        @php $count++;  @endphp
                                    @endif
                                @endforeach

                                @if ($count == 0)
                                    <tr>
                                        <td colspan="100%" class="text-center">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (!blank($cryptoWallets))
                        <div class="custom--card rounded-5">
                            <div class="card-body p-0">
                                <div class="table-responsive table-responsive--md">
                                    <table class="table custom--table mb-0">
                                        <thead>
                                            <tr>
                                                <th>@lang('Currency')</th>
                                                <th>@lang('Generated at')</th>
                                                <th>@lang('Wallet Address')</th>
                                                <th>@lang('Address QR')</th>
                                                <th>@lang('Action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cryptoWallets as $cryptoWallet)
                                                <tr>
                                                    <td>{{ $cryptoWallet->crypto->code }}</td>
                                                    <td>{{ showDateTime($cryptoWallet->created_at) }}</b></td>
                                                    <td class="copy-text">{{ $cryptoWallet->wallet_address }}</td>
                                                    <td class="copy-text">
                                                        {!! \QrCode::size(100)->generate($cryptoWallet->wallet_address) !!}
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-outline--base copy-address"><i
                                                                class="las la-copy"></i> @lang('Copy')</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if (blank($cryptoWallets))
                                        <x-no-data message="No wallet found"></x-no-data>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{--            @if ($cryptoWallets->hasPages()) --}}
            {{--                <div class="pagination-wrapper"> --}}
            {{--                    {{ $cryptoWallets->links() }} --}}
            {{--                </div> --}}
            {{--            @endif --}}
        </div>
    </section>
@endsection

@push('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function($) {
            "use strict";
            $('.select').select2();
            $('.copy-address').on('click', function() {
                let textToCopy = $(this).closest('tr').find('.copy-text').text();
                let element = document.createElement('input');
                element.value = textToCopy;
                element.id = "walletAddress";
                document.body.appendChild(element);

                var copyText = document.getElementById("walletAddress");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                notify('success', 'Copied')
                document.getElementById("walletAddress").remove();
            });

            $('#wallets_id').on('change', function() {
                //let url = '{{ route('user.wallets.single', [0, 0]) }}';
                let url = 'https://bchindex.com/user/single-wallet';
                let value = $(this).val();
                if (value == '') {
                    window.location = '{{ route('user.wallets') }}';
                } else {
                    let id_code = value.split("|");
                    window.location = url + '/' + id_code[0] + '/' + id_code[1];
                }
            });
            $('.confirm').on('click', function() {
                var msg =
                    "Important Notice.! Dear user, Deposit & Withdraw will normally take 10-15 minutes. But sometimes it takes more than average time. As Deposit & Withdraw will totally depend upon the Network Confirmations. If you will face any issue regarding deposit or withdraw, you may contact on our official mail (p2p@bchindex.com)";
                return confirm(msg);
            });
        })(jQuery);
    </script>
@endpush
