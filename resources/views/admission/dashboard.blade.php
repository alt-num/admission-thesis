<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Dashboard</title>
</head>
<body>
    <h1>Admission Dashboard (TODO)</h1>

    <p>Logged in as: <strong>{{ Auth::guard('admission')->user()->username }}</strong></p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>

