@extends('layout')

@section('title', 'Message Created')

@section('content')
    <h2>Message Created Successfully!</h2>
    <p><strong>Identifier:</strong> {{ $identifier }}</p>
    <p><strong>Decryption Key:</strong> {{ $decryption_key }}</p>
    <p><strong>Expires At:</strong> {{ $expires_at }}</p>
    <a href="/read">Go to Read Message</a>
@endsection
