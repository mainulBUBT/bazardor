<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', translate('messages.BazarDor Admin'))</title>

        <!-- Custom fonts for this template-->
        <link href="{{ asset('public/assets/admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="{{ asset('public/assets/admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('public/assets/admin/css/custom.css') }}" rel="stylesheet">
        <!-- Toastr CSS -->
        <link href="{{ asset('public/assets/admin/vendor/toastr/toastr.css') }}" rel="stylesheet"/>  
        <!-- SweetAlert2 CSS -->
        <link href="{{ asset('public/assets/admin/vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
            <!-- Select2 CSS -->
        <link href="{{ asset('public/assets/admin/vendor/select2/select2.min.css') }}" rel="stylesheet" />

          <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.css" />

        <style>
            .leaflet-control-geocoder-icon {
                background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23666"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>');
            }
            .address-loading {
                background-image: url('data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==');
                background-repeat: no-repeat;
                background-position: right 10px center;
            }
        </style>
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
        <script src="{{ asset('public/assets/admin/vendor/jquery/jquery.min.js') }}"></script>
        <!-- Select2 JS -->
        <script src="{{ asset('public/assets/admin/vendor/select2/select2.min.js') }}"></script>
        <script src="{{ asset('public/assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{ asset('public/assets/admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

        <!-- Custom scripts for all pages-->
        <script src="{{ asset('public/assets/admin/js/sb-admin-2.min.js') }}"></script>

        <!-- Toastr JS -->
        <script src="{{ asset('public/assets/admin/vendor/toastr/toastr.js') }}"></script>
        <!-- SweetAlert2 JS -->
        <script src="{{ asset('public/assets/admin/vendor/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://unpkg.com/leaflet-geosearch@3.11.0/dist/geosearch.umd.js"></script>    

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