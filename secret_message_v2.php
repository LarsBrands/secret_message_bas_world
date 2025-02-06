<?php

// Include necessary PHP extensions
if (!extension_loaded('openssl')) {
    die("OpenSSL extension is required.");
}

// Configuration
const STORAGE_FILE = 'messages.json';

/**
 * Encrypt a message
 *
 * @param string $message
 * @param string $key
 * @return string
 */
function encryptMessage(string $message, string $key): string
{
    $iv = random_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $encrypted = openssl_encrypt($message, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . '::' . $encrypted);
}

/**
 * Decrypt a message
 *
 * @param string $encryptedMessage
 * @param string $key
 * @return string
 */
function decryptMessage(string $encryptedMessage, string $key): string
{
    $decoded = base64_decode($encryptedMessage);
    [$iv, $encrypted] = explode('::', $decoded, 2);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

/**
 * Save messages to storage
 *
 * @param array $messages
 */
function saveMessages(array $messages): void
{
    file_put_contents(STORAGE_FILE, json_encode($messages));
}

/**
 * Load messages from storage
 *
 * @return array
 */
function loadMessages(): array
{
    if (!file_exists(STORAGE_FILE)) {
        return [];
    }
    return json_decode(file_get_contents(STORAGE_FILE), true) ?? [];
}

/**
 * Generate a unique identifier
 *
 * @return string
 */
function generateIdentifier(): string
{
    return bin2hex(random_bytes(8));
}

// Handle message creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $text = $_POST['text'] ?? '';
    $recipient = $_POST['recipient'] ?? '';
    $expiry = $_POST['expiry'] ?? 'read-once';
    $decryptionKey = bin2hex(random_bytes(16));

    if (empty($text) || empty($recipient)) {
        die("Text and recipient are required.");
    }

    $encryptedMessage = encryptMessage($text, $decryptionKey);
    $encryptedRecipient = encryptMessage($recipient, $decryptionKey);

    $messages = loadMessages();
    $identifier = generateIdentifier();

    $messages[$identifier] = [
        'recipient' => $encryptedRecipient,
        'message' => $encryptedMessage,
        'expiry' => $expiry,
        'created_at' => time(),
        'read' => false
    ];

    saveMessages($messages);

    echo "Message created. Identifier: $identifier, Decryption Key: $decryptionKey";
    exit;
}

// Handle message reading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'read') {
    $identifier = $_POST['identifier'] ?? '';
    $decryptionKey = $_POST['key'] ?? '';

    $messages = loadMessages();

    if (!isset($messages[$identifier])) {
        die("Message not found.");
    }

    $messageData = $messages[$identifier];

    if ($messageData['read']) {
        die("Message already read.");
    }

    if ($messageData['expiry'] === 'read-once') {
        unset($messages[$identifier]);
    } elseif (is_numeric($messageData['expiry'])) {
        $expiryTime = $messageData['created_at'] + (int)$messageData['expiry'];
        if (time() > $expiryTime) {
            unset($messages[$identifier]);
            saveMessages($messages);
            die("Message expired.");
        }
    }

    saveMessages($messages);

    $decryptedMessage = decryptMessage($messageData['message'], $decryptionKey);
    $decryptedRecipient = decryptMessage($messageData['recipient'], $decryptionKey);

    echo "Recipient: $decryptedRecipient ---------- Message: $decryptedMessage";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypted Message Sharing</title>
</head>
<body>
    <h1>Encrypted Message Sharing</h1>

    <h2>Create a Message</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <label>Message Text:</label><br>
        <textarea name="text" required></textarea><br>
        <label>Recipient Identifier:</label><br>
        <input type="text" name="recipient" required><br>
        <label>Expiry ("read-once" or seconds):</label><br>
        <input type="text" name="expiry" required><br>
        <button type="submit">Create Message</button>
    </form>

    <h2>Read a Message</h2>
    <form method="POST">
        <input type="hidden" name="action" value="read">
        <label>Message Identifier:</label><br>
        <input type="text" name="identifier" required><br>
        <label>Decryption Key:</label><br>
        <input type="text" name="key" required><br>
        <button type="submit">Read Message</button>
    </form>
</body>
</html>
