@extends('layout')

@section('title', 'Read Message')

@section('content')
    <h2>Read a Secret Message</h2>
    @if (isset($error))
        <p style="color: red;">{{ $error }}</p>
    @endif
    @if (isset($message))
        <p><strong>Decrypted Message:</strong> {{ $message }}</p>
    @else
        <form action="/read" method="POST">
            @csrf
            <label for="identifier">Message Identifier:</label>
            <input type="text" name="identifier" id="identifier" required><br><br>

            <label for="decryption_key">Decryption Key:</label>
            <input type="text" name="decryption_key" id="decryption_key" required><br><br>

            <button type="submit">Read Message</button>
        </form>
    @endif
@endsection
