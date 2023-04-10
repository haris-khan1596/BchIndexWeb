@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $balance = ($wallets->balance - 1) / $wallets->crypto->rate; // Replace with actual balance
    @endphp
    <style>
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 30px;
        }

        #max-btn {
            background-color: #3B926C;
            color: white;
            border-color: #3B926C;
        }

        #transfer-btn {
            background-color: #3B926C;
            color: white;
            border-color: #3B926C;
        }
    </style>
    <form action="{{ url('user/transfer_p2p') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="transfer_from">Transfer From</label>
            <input id="p2p" name="p2p" value="P2P Wallet" disabled>
        </div>
        <div class="form-group">
            <label for="transfer_to">Transfer To</label>
            <input id="spot" name="spot" value="Spot Wallet" disabled>
        </div>
        <div>
            <input id="wallets_id" name="wallets_id" type="hidden" value={{ $Wid }}>
        </div>
        {{-- <ul style="display: none" class="btn-list justify-content-center mb-4">
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
        </ul> --}}
        <div class="form-group">
            <label for="amount">Amount ( {{ $wallets->crypto->code }} )</label>
            <div class="input-group">
                <input id="amount" name="amount" type="number" step="any" class="form-control" required>
                <div class="input-group-append mx-2">
                    <button id="max-btn" type="button" class="btn btn-outline-secondary">Max</button>
                </div>
            </div>
            <small class="form-text text-muted">Available balance: {{ $balance }}</small>
            <div class="form-text text-muted">Transaction Fee: {{ $transaction_charges }}%</div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary" id="transfer-btn" onclick="checkFunds()">Transfer</button>
        </div>
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            // Set transfer amount to maximum balance on click
            $('#max-btn').on('click', function() {
                var max_balance = {{ $balance }}; // Replace with actual maximum balance
                $('#amount').val(max_balance);
            });
        });

        function checkFunds() {
            var amount = $('#amount').val();
            var max_balance = {{ $balance }}; // Replace with actual maximum balance
            if (amount > max_balance) {
                alert('Insufficient funds');
            }
        }
    </script>
@endpush
