<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    private $storagePath = 'messages'; // Folder to store messages

    public function __construct()
    {
        // Ensure the storage directory exists
        if (!Storage::exists($this->storagePath)) {
            Storage::makeDirectory($this->storagePath);
        }
    }

    // Show the form to create a new message
    public function showCreateForm()
    {
        return view('create-message');
    }

    // Handle message creation
    public function createMessage(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'recipient' => 'required|string',
            'expiry_type' => 'required|in:time,read-once',
            'expiry_minutes' => 'nullable|integer|min:1',
        ]);

        // Generate unique identifier and encryption key
        $identifier = uniqid('msg_');
        $encryptionKey = base64_encode(random_bytes(16));

        // Encrypt the message text
        $encryptedText = Crypt::encryptString($validated['text']);

        // Calculate expiry time if the expiry type is "time"
        $expiry = null;
        if ($validated['expiry_type'] === 'time') {
            $expiry = now()->addMinutes((int) $validated['expiry_minutes'] ?? 60)->timestamp;
        }

        // Create message payload
        $messagePayload = [
            'identifier' => $identifier,
            'recipient' => $validated['recipient'],
            'text' => $encryptedText,
            'encryption_key' => $encryptionKey,
            'expires_at' => $expiry, // Null for "read-once"
            'is_read' => false,
            'expiry_type' => $validated['expiry_type'],
        ];

        // Save the message as a JSON file
        Storage::put("{$this->storagePath}/{$identifier}.json", json_encode($messagePayload));

        return view('message-created', [
            'identifier' => $identifier,
            'decryption_key' => $encryptionKey,
            'expires_at' => $expiry ? now()->timestamp($expiry)->toDateTimeString() : 'Read Once',
        ]);
    }

    // Show the form to read a message
    public function showReadForm()
    {
        return view('read-message');
    }

    // Handle reading a message
    public function readMessage(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'decryption_key' => 'required|string',
        ]);

        $filePath = "{$this->storagePath}/{$validated['identifier']}.json";

        // Check if the file exists
        if (!Storage::exists($filePath)) {
            return view('read-message', ['error' => 'Message not found']);
        }

        // Read the message from the file
        $messageData = json_decode(Storage::get($filePath), true);

        // Check if the message has expired
        if ($messageData['expiry_type'] === 'time' && now()->timestamp > $messageData['expires_at']) {
            Storage::delete($filePath); // Delete expired message
            return view('read-message', ['error' => 'Message has expired']);
        }

        // Check if the message is "read-once" and has already been read
        if ($messageData['expiry_type'] === 'read-once' && $messageData['is_read']) {
            Storage::delete($filePath); // Delete the message after it's been read
            return view('read-message', ['error' => 'Message no longer available']);
        }

        // Verify the decryption key
        if ($validated['decryption_key'] !== $messageData['encryption_key']) {
            return view('read-message', ['error' => 'Invalid decryption key']);
        }

        // Decrypt the message text
        $decryptedText = Crypt::decryptString($messageData['text']);

        // Mark the message as read and delete if "read-once"
        if ($messageData['expiry_type'] === 'read-once') {
            Storage::delete($filePath); // Delete immediately after reading
        } else {
            $messageData['is_read'] = true; // Mark as read if not "read-once"
            Storage::put($filePath, json_encode($messageData));
        }

        return view('read-message', ['message' => $decryptedText]);
    }
}
