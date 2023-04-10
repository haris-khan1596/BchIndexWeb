@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $totalBalance = 0;
        foreach ($data as $wallet) {
            $totalBalance += $wallet->balance * $wallet->rate;
        }
        $totalBalance = number_format($totalBalance, 3);
    @endphp
    @php $cryptoImage = fileManager()->crypto(); @endphp
    <section class="pt-60 pb-60 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-4">
                        <h6>Estimated Balance (Spot Wallet):
                        </h6>
                        <p>USD <span style="color: #3B926C">{{ $totalBalance }}</span></p>
                    </div>
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table id="assets-table" class="table table-borderless table-hover">
                            <thead>
                                <tr style="background-color: #F9F9F9">
                                    <th>Asset</th>
                                    <th>Symbol</th>
                                    <th>On Order</th>
                                    <th>Available Balance</th>
                                    <th>Total Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through your data and populate the table rows -->
                                @foreach ($data as $wallet)
                                    <tr style="background-color: #F9F9F9">
                                        <td>{{ $wallet->name }}</td>
                                        <td><img style="width: 30px; height: 30px;" src="{{ getImage($cryptoImage->path . '/' . $wallet->image, $cryptoImage->size) }}" alt="@lang('image')"></td>
                                        <td>{{ $wallet->balance -  $wallet->available_balance}}</td>
                                        <td>{{ $wallet->available_balance }}</td>
                                        <td>{{ $wallet->balance }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.wallets') }}" class="mx-3" data-toggle="tooltip"
                                                    data-placement="top" title="Deposit"><img
                                                        src="{{ getImage('assets/images/icons/deposit.svg') }}"
                                                        alt="deposit"></a>
                                                <a href="{{ route('user.wallets') }}" class="mx-3" data-toggle="tooltip"
                                                    data-placement="top" title="Withdraw"><img
                                                        src="{{ getImage('assets/images/icons/withdraw.svg') }}"
                                                        alt="withdraw"></a>
                                                <a href="{{ route('user.exchange') }}" class="mx-3" data-toggle="tooltip"
                                                    data-placement="top" title="Trade"><img
                                                        src="{{ getImage('assets/images/icons/trade.svg') }}"
                                                        alt="trade"></a>
                                                <a href="#" class="mx-3" data-toggle="tooltip" data-placement="top"
                                                    title="Swap"><img
                                                        src="{{ getImage('assets/images/icons/swap.svg') }}"
                                                        alt="swap"></a>
                                                <a href="{{ url('/user/' . $wallet->crypto_currency_id . '/' . $wallet->user_id . '/transfer_p2s') }}"
                                                    class="mx-3" data-toggle="tooltip" data-placement="top"
                                                    title="Transfer to P2P"><img
                                                        src="{{ getImage('assets/images/icons/transferTo.png') }}"
                                                        alt="transfer"></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <!-- Include the DataTables CSS and JS files -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize the DataTable
            var table = $('#assets-table').DataTable({
                "paging": true,
                "searching": true,
                "lengthChange": true,
                "pageLength": 10 // Set the default number of rows per page
            });

            // Add a search box and row count selector to the table
            table.columns().every(function() {
                var column = this;
                var header = $(column.header());

                // Add a row count selector for this column
                if (header.text() === '') {
                    var select = $('<select>')
                        .appendTo(header)
                        .on('change', function() {
                            table.page.len($(this).val()).draw();
                        });

                    // Populate the row count selector
                    var pageLengths = [10, 25, 50, 100];
                    pageLengths.forEach(function(pageLength) {
                        var option = $('<option>', {
                            value: pageLength,
                            text: pageLength
                        }).appendTo(select);
                    });
                }
            });
        });
    </script>
@endpush
