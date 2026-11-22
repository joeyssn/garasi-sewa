<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Garage Rental')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Garage Rental</a>
        @auth
            <ul class="navbar-nav me-auto">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.garages.index') }}">Garages</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.tenants.index') }}">Verify Registrants</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.extensions.index') }}">Extend Rental</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}">Reports</a></li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('rentals.index') }}">My Rentals</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('rentals.create') }}">New Rental</a></li>
                @endif
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
                <button class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        @endauth
    </div>
</nav>
<main class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>