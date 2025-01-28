<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Message Sharing')</title>
</head>
<body>
<header>
    <h1>Laravel Encrypted Message Sharing</h1>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/create">Create Message</a></li>
            <li><a href="/read">Read Message</a></li>
        </ul>
    </nav>
</header>
<main>
    @yield('content')
</main>
<footer>
    <p>&copy; {{ date('Y') }} Message Sharing App</p>
</footer>
</body>
</html>
