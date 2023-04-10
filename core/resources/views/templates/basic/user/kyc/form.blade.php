@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @php
        $kycContent = getContent('kyc.content', true);
    @endphp
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3B926C;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <section class="pt-60 pb-60">
        <div class="container">
            @if ($user->kv != 1)
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">@lang('KYC Verification Pending')</h4>
                    <hr>
                    <p class="mb-0">{{ __(@$kycContent->data_values->kyc_pending) }} <a
                            href="{{ route('user.kyc.data') }}" class="text--base">@lang('See KYC Data')</a></p>
                </div>
            @endif
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body">
                            <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @foreach($formData as $k=> $item)
                                    <div class="form-group">
                                        <label>@lang($item->name)</label>
                                        @if(isset($item->status) && !empty($item->status))
                                            <p style="color: #3B926C;">{{$item->status}}</p>
                                        @else
                                            @if($item->label=='upload_selfie')
                                                <div id="my_camera" class="mb-4"></div>
                                                <input @class('form-control') type=button value="Take Selfie"
                                                       id="on_cam">
                                                <input @class('form-control') type=button value="Take Selfie"
                                                       data_type="{{$item->label}}" id="take_snapshot">
                                                <input @class('form-control') type=button value="Off Camera"
                                                       id="off_cam">
                                                <input type="hidden" name="image" id="image" class="image-tag">
                                                <div class="col-md-6">
                                                    <div id="results"></div>
                                                </div>
                                            @else
                                                <input type="{{$item->type}}" name="{{$item->label}}"
                                                       id="{{$item->label}}" class="form-control"
                                                       oninput="submitData('{{$item->label}}')">
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                                @if ($user->kv != 2)
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="loading" tabindex="-1" role="dialog" aria-labelledby="loading" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="loader m-auto my-5"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="approved" tabindex="-1" role="dialog" aria-labelledby="approved" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p style="color: #3B926C;">Approved</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="error" tabindex="-1" role="dialog" aria-labelledby="error" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <p style="color: red;">Error in Upload</p>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script>
        Webcam.set({
            width: 490,
            height: 350,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        $('#off_cam').hide();
        $('#take_snapshot').hide();
        $("#on_cam").click(function () {
            Webcam.attach('#my_camera');
            $('#my_camera').show();
            $('#off_cam').show();
            $('#take_snapshot').show();
            $('#on_cam').hide();
        });
        $("#off_cam").click(function () {
            Webcam.reset();
            $('#on_cam').show();
            $('#my_camera').hide();
            $('#off_cam').hide();
            $('#take_snapshot').hide();
        });
        $("#take_snapshot").click(function () {
            Webcam.snap(function (data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
            });
            $('#off_cam').click();
            var type = $(this).attr('data_type');
            submitData(type);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function dataURLtoFile(dataurl, filename) {

            var arr = dataurl.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                n = bstr.length,
                u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], filename, {type: mime});
        }

        function submitData(type) {
            $('#loading').modal('show');
            // let formData = new FormData(this);
            var fd = new FormData();
            // Append data
            fd.append('type', type);

            if (type == 'upload_selfie') {
                //var file = $('#image').val();
                var file = $('#results img').attr('src');
                file = dataURLtoFile(file, 'selfie.jpeg');
                fd.append('file', file);
            } else {
                var file = document.getElementById(type).files;
                fd.append('file', file[0]);
            }
            // fd.append('_token',CSRF_TOKEN);
            console.log('here', fd);
            //return false;

            $.ajax({
                type: 'POST',
                url: "{{ url('user/kyc-submit-type') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (data) {
                    if (data.status != 200) {
                        loadingModal(1)
                    } else {
                        loadingModal(0)
                    }
                }
            });

        };


        function loadingModal(error) {
            console.log(error)
            if (error) {
                setTimeout(errorModal, 5000);
            } else {
                setTimeout(approvedModal, 5000);

            }

        }

        function errorModal() {
            $('#loading').modal('hide');
            $('#error').modal('show');
            setTimeout("$('#error').modal('hide');", 1000);

        }

        function approvedModal() {
            $('#loading').modal('hide');
            $('#approved').modal('show');
            setTimeout("$('#approved').modal('hide');", 1000);
            setTimeout(location.reload(), 3000);

        }
    </script>

@endsection
