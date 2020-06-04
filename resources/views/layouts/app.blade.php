<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Build your PC using the latest prices from local vendors.">
    <meta name="author" content="Liam Demafelix, The Overclocks Community">
    <title>PC Builder - Overclocks</title>
    <link rel="canonical" href="https://builder.overclocks.org/">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column h-100">
<header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                Overclocks PC Builder
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="https://overclocks.org/">Community Forums</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://overclocks.org/forums/build-showcase.5/">Builds Showcase</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    @if (!session()->has('xenforo'))
                        <li class="nav-item">
                            <a class="nav-link show-login-modal" href="javascript:void(0)">Sign In</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link sign-out" href="{{ route('logout') }}">Hi, {{ session()->get('xenforo')->username }}! (Sign Out)</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
</header>
<main role="main" class="flex-shrink-0">
    @yield('content')

    @if (!session()->has('xenforo'))
        <div class="modal fade" id="modalLogin" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="login-form" method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Sign In</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                Sign in using your <a href="https://overclocks.org/">community forums</a> account.
                            </div>
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input class="form-control" name="username" id="username" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password</label>
                                <input class="form-control" type="password" name="password" id="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</main>

<footer class="footer mt-auto py-3">
    <div class="container">
        <span class="text-muted">Copyright &copy; {{ date('Y') }} Overclocks</span>
    </div>
</footer>
<script>
    window.Promise || document.write('<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"><\/script>');
</script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('js')
@if (session()->has('success'))
    <script>
        $(function () {
            Toast.fire({
                icon: 'success',
                title: '{{ session()->get('success') }}'
            });
        });
    </script>
@endif
</body>
</html>