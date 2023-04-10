<link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>



@if (session()->has('notify'))
    @foreach (session('notify') as $msg)
        <script>
            "use strict";
            @if ($msg[0] == 'error')
                iziToast.error({
                    title: "Access Denied",
                    message: "{{ __($msg[1]) }}",
                    position: "topCenter",
                    icon: 'ico-error',
                    iconColor: '#F44336',
                });
            @else
                iziToast.success({
                    title: "Access Granted",
                    message: "{{ __($msg[1]) }}",
                    position: "topCenter",
                    icon: 'ico-success',
                    iconColor: '#3B926C',
                });
            @endif
        </script>
    @endforeach
@endif

@if (isset($errors) && $errors->any())
    @php
        $collection = collect($errors->all());
        $errors = $collection->unique();
    @endphp

    <script>
        "use strict";
        @foreach ($errors as $error)
            iziToast.error({
                title: "Access Denied", // Add a title above the message
                message: '{{ __($error) }}',
                position: "topCenter",
                icon: 'ico-error',
                iconColor: '#F44336',
            });
        @endforeach
    </script>
@endif
<script>
    "use strict";

    function notify(status, message) {
        var title = '';
        var icon = '';

        if (status == 'error') {
            title = 'Access Denied';
            icon = 'bi-exclamation-circle-fill';
        } else if (status == 'success') {
            title = 'Access Granted';
            icon = 'bi-check-circle-fill';
        }

        if (typeof message == 'string') {
            iziToast[status]({
                title: title,
                message: message,
                position: "topCenter",
                icon: 'ico-error',
                iconColor: '#F44336',
            });
        } else {
            $.each(message, function(i, val) {
                iziToast[status]({
                    title: title,
                    message: val,
                    position: "topCenter",
                    icon: 'ico-success',
                    iconColor: '#3B926C',
                });
            });
        }
    }
</script>
