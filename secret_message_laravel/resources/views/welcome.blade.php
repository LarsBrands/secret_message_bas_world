@extends('layout')

@section('title', 'Welcome')

@section('content')
    <h2>Welcome to the Encrypted Message Sharing App</h2>
    <p>Use the buttons below to get started:</p>
    <div style="margin-top: 20px;">
        <a href="/create" style="text-decoration: none;">
            <button style="padding: 10px 20px; font-size: 16px;">Create a New Message</button>
        </a>
        <a href="/read" style="text-decoration: none; margin-left: 10px;">
            <button style="padding: 10px 20px; font-size: 16px;">Read an Existing Message</button>
        </a>
    </div>
@endsection
