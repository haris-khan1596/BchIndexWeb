<div class="custom--card">
    <div class="card-body p-0">
        <div class="table-responsive--md">
            <table class="table custom--table mb-0">
                <thead>
                    <tr>
                        <th>@lang('Seller')</th>
                        <th>@lang('Payment method')</th>
                        <th>@lang('Rate')</th>
                        <th>@lang('Limits')</th>
                        <th>@lang('Avg. Trade Speed')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>

                <tbody>
                    @php $i = 0; @endphp
                    
                    @foreach ($ads as $ad)
                        @php $maxLimit = getMaxLimit($ad->user->wallets, $ad); @endphp

                        @if ($maxLimit >= $ad->min)
                            @php
                                $i++;
                            @endphp

                            <tr class="@if (auth()->user() && auth()->user()->id == $ad->user->id) own-trade-color @endif">
                                <td>
                                    <a href="{{ route('public.profile', $ad->user->username) }}" class="text--base">{{ __($ad->user->username) }}</a>
                                </td>

                                <td> {{ __($ad->fiatGateway->name) }}</td>

                                <td><b>{{ getRate($ad) }} {{ __($ad->fiat->code) }}/ {{ __($ad->crypto->code) }}</b></td>

                                <td>{{ showAmount($ad->min) }} - {{ showAmount($maxLimit) }} {{ __($ad->fiat->code) }}</td>

                                <td>{{ avgTradeSpeed($ad) }}</td>

                                <td>
                                    <a href="{{ route('user.trade.request.new', $ad->id) }}" class="btn--base btn-sm">@lang('Buy')</a>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if (!$i)
                        <td colspan="100%" class="text-center">@lang('No data found')</td>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
