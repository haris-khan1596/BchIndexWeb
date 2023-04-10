@extends('admin.layouts.app')

@section('panel')
    <style>
        label {
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1.2em;
        }

        input[type="submit"] {
            padding: 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.2em;
            cursor: pointer;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 50vw;
            width: 40%;
            height: 50%;
            overflow: auto;
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #F8D7DA;
            padding: 20px;
            width: 100%;
        }
    </style>


    @if (session('error') != null)
        {{--  <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="alert alert-danger ">
                {{ session('error') }}
            </div>
        </div>  --}}
        <div id="myModal" class="modal ">

            <!-- Modal content -->
            <div class="modal-content">
                <p class="alert alert-danger">{{ session('error') }}</p>
            </div>

        </div>
        <script>
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    @endif
    <!-- Create Post Form -->
    <div class="container">

        <h4 class="mb-4">Admin Balance ${{ $admin->balance }}</h4>
        <h3>Set Transaction Charges (P2P to Spot)</h3>

        <form method="POST" action="{{ url('admin/transaction_charge_p2p_to_spot') }}">
            @csrf
            <div class="form-group">
                <label for="percentage">Enter Percentage (0% to 100%)</label>
                <input type="number" step="any" min="0" name="percentage" id="percentage" class="form-control"
                    value="{{ $transaction_charge_p2p_to_spot->percent_charge }}">
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #3B926C">Update</button>
        </form>

        <h3 class="mt-5">Set Transaction Charges (Spot to P2P)</h3>
        <form method="POST" action="{{ url('admin/transaction_charge_spot_to_p2p') }}">
            @csrf

            <div class="form-group">
                <label for="percentage">Enter Percentage (0% to 100%)</label>
                <input type="number" step="any" min="0" name="percentage" id="percentage" class="form-control"
                    value="{{ $transaction_charge_spot_to_p2p->percent_charge }}">
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #3B926C">Update</button>
        </form>

        <h3 class="mt-5">Bot <span>(Note : transaction charges for bot should'nt be more than 5 to 10%)</span></h3>
        <form method="POST" action="{{ url('admin/setBot') }}">
            @csrf

            <div class="form-group">
                <label for="percentage">Enter Percentage (0% to 100%)</label>
                <input type="number" step="any" min="0" name="percentage" id="percentage" class="form-control"
                    value="{{ $transaction_charge_bot->percent_charge }}">
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #3B926C">Update</button>
        </form>

        <h3 class="mt-5">Spot Trading Pairs</h3>
        <form method="POST" action="{{ url('admin/coins-pairs') }}">
            @csrf

            <div class="form-group">
                <label for="percentage">Crypto Currency 1</label>
                <select id="base_coin" name="base_coin">
                    @foreach ($crypto_currencies as $currency)
                        @if ($currency->code != 'USDT')
                            <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                        @endif
                    @endforeach
                </select>


                <label for="percentage">Crypto Currency 2</label>
                <select id="quote_coin" name="quote_coin">
                    @foreach ($crypto_currencies as $currency)
                        @if ($currency->code == 'USDT')
                            <option value="{{ $currency->id }}" selected>{{ $currency->code }}</option>
                        @else
                            <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #3B926C">Add</button>
        </form>




        <h3 class="mt-5">Existing Pairs</h3>
        <div class="table-responsive">
            <table id="assets-table" class="table table-borderless table-hover">
                <thead>
                    <tr style="background-color: #F9F9F9">
                        <th>Pair Name</th>
                        <th>Crypto 1</th>
                        <th>Crypto 2</th>
                        <th>Fake Data</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coin_pairs as $coin_pair)
                        <tr style="background-color: #F9F9F9">
                            <td>{{ $coin_pair->name }}</td>
                            @php
                                $codes = explode(' - ', $coin_pair->name);
                            @endphp
                            @foreach ($codes as $item)
                                <td>{{ $item }}</td>
                            @endforeach
                            <td>
                                {{-- <input type="range" id="myRange" name="fake" min="0" max="1"
                                    value="{{ $coin_pair->fake }}"> --}}

                                <input type="checkbox" id="myCheckbox-{{$coin_pair->id}}" name="myCheckbox-{{$coin_pair->id}}"
                                    {{ $coin_pair->fake ? 'checked' : '' }}>

                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <div class="btn btn-sm btn-primary" id="Update-{{$coin_pair->id}}">Update</div>
                                </div>
                                <script>
                                    button = document.getElementById('Update-{{$coin_pair->id}}');
                                    button.addEventListener("click", function() {
                                        element = document.getElementById('myCheckbox-{{$coin_pair->id}}');
                                        URL = "{{ url('admin/coin-pair/update/' . $coin_pair->id) }}" + "/" + element.checked;
                                        window.open(URL, "_self");
                                        console.log(URL);
                                    });
                                </script>
                                <div class="btn-group" role="group">
                                    <a href="{{ url('admin/coin-pair/del/' . $coin_pair->id) }}"
                                        class="btn btn-sm btn-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>




    </div>
@endsection
