@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Initiated')</th>
                                <th>@lang('Transaction ID')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($deposits as $deposit)

                                <tr>
                                    <td>
                                        {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                    </td>
                                    <td>
                                        {{ $deposit->trx }}
                                    </td>
                                    <td>
                                        <span >{{ $deposit->user->fullname }}</span>
                                        <br>
                                        <span class="small">
                                        <a href="{{ appendQuery('search',@$deposit->user->username) }}"><span>@</span>{{ $deposit->user->username }}</a>
                                        </span>
                                    </td>
                                    <td>
                                        {{ __($deposit->crypto->symbol) }}{{ showAmount($deposit->amount,8) }} + <span class="text-danger" title="@lang('charge')">{{ showAmount($deposit->charge,8)}} {{ __($deposit->crypto->code) }}</span>
                                        <br>
                                        <strong title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->amount + $deposit->charge, 8) }} {{ __($deposit->crypto->code) }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span><span class="badge badge--success">@lang('Success')</span><br>{{diffForHumans($deposit->created_at)}}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.deposit.details', $deposit->id) }}"
                                        class="btn btn-sm btn-outline--primary ms-1">
                                            <i class="la la-desktop"></i> @lang('Details')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($deposits->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($deposits) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        @if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
            <div class="form-inline float-sm-end">
                <x-search-form placeholder="Trx number/Username"></x-search-form>
            </div>

            <form action="" method="GET">
                <div class="form-inline float-sm-end">
                    <div class="input-group">
                        <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control bg--white" data-position='bottom right' placeholder="@lang('Start date - End date')" autocomplete="off" value="{{ request()->date }}">
                        <button class="btn btn--primary input-group-text" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/datepicker.min.css')}}">
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
  <script>
    (function($){
        "use strict";
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
  </script>
@endpush
