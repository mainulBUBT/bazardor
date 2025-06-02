<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', translate('messages.BazarDor Admin'))</title>

        <!-- Custom fonts for this template-->
        <link href="/bazardor-backend/public/assets/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="/bazardor-backend/public/assets/admin/css/sb-admin-2.min.css" rel="stylesheet">
        <link href="/bazardor-backend/public/assets/admin/css/custom.css" rel="stylesheet">
        <!-- Toastr CSS -->
        <link href="{{ asset('public/assets/admin/vendor/toastr/toastr.css') }}" rel="stylesheet"/>  
        <!-- SweetAlert2 CSS -->
        <link href="{{ asset('public/assets/admin/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
        @stack('styles')
    </head>
    <body id="page-top">
        <!-- Page Wrapper -->
        <div id="wrapper">

            <!-- Sidebar -->
            @include('layouts.admin.sidebar')
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <!-- Topbar -->
                    @include('layouts.admin.topbar')
                    <!-- End of Topbar -->

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                    <!-- /.container-fluid -->

                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                @include('layouts.admin.footer')
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        @include('layouts.admin.logout-modal')

        <!-- Bootstrap core JavaScript-->
        <script src="/bazardor-backend/public/assets/admin/vendor/jquery/jquery.min.js"></script>
        <script src="/bazardor-backend/public/assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="/bazardor-backend/public/assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="/bazardor-backend/public/assets/admin/js/sb-admin-2.min.js"></script>

        <!-- Toastr JS -->
        <script src="{{ asset('public/assets/admin/vendor/toastr/toastr.js') }}"></script>
        <!-- SweetAlert2 JS -->
        <script src="{{ asset('public/assets/admin/vendor/sweetalert2/sweetalert2.min.js') }}"></script>
        {!! Toastr::message() !!}

        <script>
        function statusAlert(obj) {
            let url = $(obj).data('url');
            console.log(url);
            let checked = $(obj).prop("checked");
            let status = checked === true ? 1 : 0;
    
            Swal.fire({
                title: '{{translate("messages.are_you_sure")}}?',
                text: '{{translate("messages.want_to_change_status")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#6c757d',
                cancelButtonText: '{{translate("messages.no")}}',
                confirmButtonText: '{{translate("messages.yes")}}',
                reverseButtons: true
            }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status, 
                                id: $(obj).attr('name'),
                            },
                            success: function (response) {
                                toastr.success(response.message || "{{translate('messages.status_changed_successfully')}}");
                            },
                            error: function (xhr) {
                                // Revert the toggle state
                                $(obj).prop('checked', !checked);
                                
                                // Show error message
                                let errorMessage = "{{translate('messages.status_change_failed')}}";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                toastr.error(errorMessage);
                            }
                        });
                    }
                    else if (result.dismiss === 'cancel') {
                        if (status === 1) {
                            $('#' + obj.id).prop('checked', false)
                        } else if (status === 0) {
                            $('#'+ obj.id).prop('checked', true)
                        }
                        toastr.info("{{translate("messages.status_is_not_changed")}}");
                    }
                }
            )
        }
    
        // ======= SEARCH FILTER ======= //
        function setFilter(url, key, filter_by) {
            var nurl = new URL(url);
            nurl.searchParams.set(filter_by, key);
            location.href = nurl;
        }

        function getUrlParameter(sParam) {
            const searchParams = new URLSearchParams(window.location.search);
            return searchParams.has(sParam) ? searchParams.get(sParam) : null;
        }

    
        function formAlert(id, message) {
            console.log(id)
            Swal.fire({
                title: '{{translate("messages.are_you_sure")}}?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#6c757d',
                confirmButtonColor: '#007bff',
                cancelButtonText: '{{translate("messages.no")}}',
                confirmButtonText: '{{translate("messages.yes")}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#'+id).submit()
                }
            })
        }
        </script>

        @if ($errors->any())
            <script>
                @foreach($errors->all() as $error)
                toastr.error('{{$error}}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
                @endforeach
            </script>
        @endif
        @stack('scripts')
        
    </body>
</html>