@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $balance = $wallets[0]->available_balance; // Replace with actual balance
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
    <form action="{{ url('user/' . $Cid . '/' . $Uid . '/transfer_p2s') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="transfer_from">Transfer From</label>
            {{-- <select id="transfer_from" name="transfer_from" class="form-control">
                <option value="Spot Wallet">Spot Wallet</option>
            </select> --}}
            <input id="spot" name="spot" value="Spot Wallet" disabled>
        </div>
        <div class="form-group">
            <label for="transfer_to">Transfer To</label>
            <input id="p2p" name="p2p" value="P2P Wallet" disabled>
        </div>
        <div class="form-group">
            <label for="amount">Amount ({{ $cryptoCurr->code }})</label>
            <div class="input-group">
                <input id="amount" name="amount" type="number" step="any" class="form-control" required>
                <div class="input-group-append mx-2">
                    <button id="max-btn" type="button" class="btn btn-outline-secondary">Max</button>
                </div>
            </div>
            <small class="form-text text-muted">Available balance: {{ $wallets[0]->available_balance }}
                {{ $cryptoCurr->code }}</small>
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
            // // Swap transfer from/to inputs on click
            // $('#transfer_from, #transfer_to').on('change', function() {
            //     var transfer_from = $('#transfer_from').val();
            //     var transfer_to = $('#transfer_to').val();
            //     $('#transfer_from').val(transfer_to);
            //     $('#transfer_to').val(transfer_from);
            // });

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
