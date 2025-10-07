<html class="loaded" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <title>Login - IDMS</title>
    <link rel="apple-touch-icon" href="{{ url('app-assets') }}/images/ico/IDMS.png">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('app-assets') }}/images/ico/IDMS.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/vendors/css/forms/icheck/custom.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/app.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/core/menu/menu-types/vertical-content-menu.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="{{ url('app-assets') }}/css/pages/login-register.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('assets') }}/css/style.css">
    <!-- END Custom CSS-->
    <style type="text/css">
        .jqstooltip {
            position: absolute;
            left: 0px;
            top: 0px;
            visibility: hidden;
            background: rgb(0, 0, 0) transparent;
            background-color: rgba(0, 0, 0, 0.6);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";
            color: white;
            font: 10px arial, san serif;
            text-align: left;
            white-space: nowrap;
            padding: 5px;
            border: 1px solid white;
            z-index: 10000;
        }

        .jqsfield {
            color: white;
            font: 10px arial, san serif;
            text-align: left;
        }
    </style>
</head>

<body class="vertical-layout vertical-content-menu 1-column blank-page blank-page pace-done menu-expanded" data-open="click" data-menu="vertical-content-menu" data-col="1-column">
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="flexbox-container">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-md-4 col-10 box-shadow-2 p-0">
                            <div class="card border-grey border-lighten-3 m-0">
                                <div class="card-header border-0">
                                    <div class="card-title text-center mb-0">
                                        <img src="{{ url('app-assets') }}/images/logo/IDMS.png" alt="branding logo" style="max-width: 250px;">
                                    </div>
                                    <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2 mb-0"><span>Login IDMS</span></h6>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form-horizontal form-simple" action="{{ route('login') }}" method="POST">
                                            @csrf
                                            <fieldset class="form-group position-relative has-icon-left mb-1">
                                                <input type="text" class="form-control form-control-lg input-lg @error('email') is-invalid @enderror" id="user-name" name="email" value="{{ old('email') }}" required placeholder="Your E-mail" autofocus>
                                                <div class="form-control-position d-flex align-items-center justify-content-center" style="width: 40px; top: -2px">
                                                    <i class="ft-user"></i>
                                                </div>
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </fieldset>
                                            <fieldset class="form-group position-relative has-icon-left">
                                                <div class="d-flex align-items-center">
                                                    <input type="password" class="form-control form-control-lg input-lg @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Enter Password">
                                                    <div class="form-control-position d-flex align-items-center justify-content-center" style="width: 40px; top: -2px">
                                                        <i class="fa fa-key"></i>
                                                    </div>
                                                </div>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </fieldset>
                                            <button type="submit" class="btn btn-info btn-lg btn-block"><i class="ft-unlock"></i> {{ __('Login') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- BEGIN VENDOR JS-->
    <script src="{{ url('app-assets') }}/vendors/js/vendors.min.js"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ url('app-assets') }}/vendors/js/ui/jquery.sticky.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/charts/jquery.sparkline.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/ui/headroom.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/forms/icheck/icheck.min.js"></script>
    <script src="{{ url('app-assets') }}/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="{{ url('app-assets') }}/js/core/app-menu.js"></script>
    <script src="{{ url('app-assets') }}/js/core/app.js"></script>
    <!-- END ROBUST JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{ url('app-assets') }}/js/scripts/ui/breadcrumbs-with-stats.js"></script>
    <script src="{{ url('app-assets') }}/js/scripts/forms/form-login-register.js"></script>
    <!-- END PAGE LEVEL JS-->

</body>

</html>