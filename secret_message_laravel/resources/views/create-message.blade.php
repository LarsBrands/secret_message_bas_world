@extends('layout')

@section('title', 'Create Message')

@section('content')
    <h2>Create a Secret Message</h2>
    <form action="/create" method="POST">
        @csrf
        <label for="text">Message:</label>
        <textarea name="text" id="text" required></textarea><br><br>

        <label for="recipient">Recipient:</label>
        <input type="text" name="recipient" id="recipient" required><br><br>

        <label for="expiry_type">Expiry Type:</label>
        <select name="expiry_type" id="expiry_type" required onchange="toggleExpiryTime(this.value)">
            <option value="time">Expire After Time</option>
            <option value="read-once">Read Once</option>
        </select><br><br>

        <div id="expiry-time" style="display: block;">
            <label for="expiry_minutes">Expiry Time (Minutes):</label>
            <input type="number" name="expiry_minutes" id="expiry_minutes" min="1"><br><br>
        </div>

        <button type="submit">Create Message</button>
    </form>

    <script>
        function toggleExpiryTime(value) {
            const expiryTimeDiv = document.getElementById('expiry-time');
            if (value === 'read-once') {
                expiryTimeDiv.style.display = 'none';
            } else {
                expiryTimeDiv.style.display = 'block';
            }
        }
    </script>
@endsection
