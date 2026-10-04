<!DOCTYPE html>
<html>
<head>
    <title>{{ $tenant->name }} Portal</title>
</head>
<body>
    <h1>Welcome to {{ $tenant->name }}</h1>
    <p>Fast and Reliable Internet for Maseno.</p>
    <a href="{{ route('portal.plans') }}">View Internet Plans</a>
</body>
</html>
