    @php
        $profileImage = fileManager()->userProfile();
        $user = auth()->user();
        $topImage = $trade->buyer_id == $user->id ? @$trade->seller->image :@ $trade->buyer->image;
        $authBuyer = $user->id == $trade->buyer_id;

        $lastTime = Carbon\Carbon::parse($trade->paid_at)->addMinutes($trade->window);
        $remainingMin = $lastTime->diffInMinutes(now());

        $endTime = $trade->created_at->addMinutes($trade->window);
        $remainingMinitues = $endTime->diffInMinutes(now());
    @endphp
<div class="row gy-3 mb-3 justify-content-center">
    @if ($trade->status == 0)
        @if ($authBuyer || (!$authBuyer && $endTime <= now()))
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--danger w-100 confirmationBtn" data-action="{{ route('user.trade.request.cancel', $trade->id) }}" data-question="@lang('Are you sure to cancel this trade?')">
                    <i class="las la-times-circle"></i> @lang('Cancel')
                </button>
            </div>
        @endif

        @if ($authBuyer)
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--success w-100 confirmationBtn" data-action="{{ route('user.trade.request.paid', $trade->id) }}" data-question="@lang('Are you sure that you have paid the amount?')">
                    <i class="las la-check-circle"></i> @lang('I Have Paid')
                </button>
            </div>
        @endif

    @endif

    @if ($trade->status == 2)
        @if (!$authBuyer || ($authBuyer && $lastTime <= now()))
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--danger w-100 confirmationBtn" data-action="{{ route('user.trade.request.dispute', $trade->id) }}" data-question="@lang('Are you sure to dispute this trade?')">
                    <i class="las la-times-circle"></i> @lang('Dispute')
                </button>
            </div>
        @endif

        @if (!$authBuyer)
            <div class="col-md-6">
                <button type="submit" class="btn btn-lg btn--success w-100 confirmationBtn" data-action="{{ route('user.trade.request.release', $trade->id) }}" data-question="@lang('Are you sure to release this trade?')">
                    <i class="las la-check-circle"></i> @lang('Release')
                </button>
            </div>
        @endif
    @endif
</div>

<x-confirmation-modal></x-confirmation-modal>
