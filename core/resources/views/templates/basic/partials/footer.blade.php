@php
    $footerContent = getContent('footer.content', true);
    $socialIconElements = getContent('social_icon.element');
    $policyElements = getContent('policy_pages.element');
@endphp
<style>
    .footer-top {
        background: #212529;
    }

    .footer-bottom {
        background: #212529;
    }
</style>
<div class="scroll-to-top">
    <span class="scroll-icon">
        <i class="fa fa-rocket" aria-hidden="true"></i>
    </span>
</div>

<footer class="footer-section bg_img"
    data-background="{{ getImage('assets/images/frontend/footer/' . @$footerContent->data_values->image, '1920x380') }}">
    <div class="footer-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-12">
                    <div class="footer-widget text-center">
                        <a href="{{ route('home') }}" class="footer-logo mb-4"><img
                                src="{{ getImage('assets/images/logoIcon/logo.png') }}" alt="image"></a>
                        <p>{{ __(@$footerContent->data_values->footer_text) }}</p>
                        <ul class="social-links mt-4 justify-content-center">
                            @foreach ($socialIconElements as $social)
                                <li><a href="{{ @$social->data_values->url }}" target="_blank">@php echo @$social->data_values->social_icon @endphp</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <hr>
            <div class="row">
                <div class="col-lg-8 col-md-6 text-md-left text-center">
                    <p>@lang('Copyright') © {{ date('Y') }} | @lang('All Right Reserved')</p>
                </div>
                <div class="col-lg-4 col-md-6 mt-md-0 mt-3">
                    <ul class="link-list justify-content-md-end justify-content-center">
                        @foreach ($policyElements as $policy)
                            <li>
                                <a
                                    href="{{ route('policy.pages', [slug(@$policy->data_values->title), $policy->id]) }}">{{ __($policy->data_values->title) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
