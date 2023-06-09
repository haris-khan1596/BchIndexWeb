@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
    $kycContent = getContent('kyc.content', true);
    @endphp

    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body">
                            <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <x-viser-form identifier="act" identifierValue="kyc"></x-viser-form>

                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
