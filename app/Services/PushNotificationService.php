<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send push notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, string $url = null): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $subscription) {
            $this->sendNotification($subscription, $title, $body, $url);
        }
    }

    /**
     * Send push notification to all subscriptions.
     */
    public function sendToAllSubscriptions(string $title, string $body, string $url = null): void
    {
        PushSubscription::chunk(100, function ($subscriptions) use ($title, $body, $url) {
            foreach ($subscriptions as $subscription) {
                $this->sendNotification($subscription, $title, $body, $url);
            }
        });
    }

    /**
     * Send a single push notification via Web Push protocol using curl.
     */
    protected function sendNotification(PushSubscription $subscription, string $title, string $body, string $url = null): void
    {
        $vapid = config('webpush.vapid');

        if (empty($vapid['public_key']) || empty($vapid['private_key'])) {
            Log::error('PushNotification: VAPID-nøkler er ikke konfigurert.');
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? '/',
        ]);

        try {
            // Build JWT for VAPID authentication
            $header = $this->base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
            $endpoint = parse_url($subscription->endpoint);
            $audience = $endpoint['scheme'] . '://' . $endpoint['host'];
            $expiration = time() + 43200; // 12 hours

            $claimPayload = $this->base64UrlEncode(json_encode([
                'aud' => $audience,
                'exp' => $expiration,
                'sub' => $vapid['subject'],
            ]));

            $signingInput = $header . '.' . $claimPayload;

            // Sign with VAPID private key
            $privateKeyPem = $this->vapidKeyToPem($vapid['private_key'], $vapid['public_key']);
            $privateKey = openssl_pkey_get_private($privateKeyPem);

            if (!$privateKey) {
                Log::error('PushNotification: Kunne ikke laste VAPID private key.');
                return;
            }

            $signature = '';
            openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

            // Convert DER signature to raw r||s format (64 bytes)
            $signature = $this->derToRaw($signature);
            $jwt = $signingInput . '.' . $this->base64UrlEncode($signature);

            // Encrypt payload using subscription keys
            $encrypted = $this->encryptPayload(
                $payload,
                $subscription->public_key,
                $subscription->auth_token,
                $subscription->content_encoding ?? 'aesgcm'
            );

            if (!$encrypted) {
                Log::error('PushNotification: Kunne ikke kryptere payload.');
                return;
            }

            // Send the request
            $headers = [
                'Authorization: vapid t=' . $jwt . ', k=' . $vapid['public_key'],
                'TTL: 2419200',
            ];

            if ($encrypted['encoding'] === 'aes128gcm') {
                $headers[] = 'Content-Type: application/octet-stream';
                $headers[] = 'Content-Encoding: aes128gcm';
            } else {
                $headers[] = 'Content-Type: application/octet-stream';
                $headers[] = 'Content-Encoding: aesgcm';
                $headers[] = 'Encryption: salt=' . $encrypted['salt'];
                $headers[] = 'Crypto-Key: dh=' . $encrypted['serverPublicKey'] . ';p256ecdsa=' . $vapid['public_key'];
            }

            $ch = curl_init($subscription->endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $encrypted['cipherText'],
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Remove expired/invalid subscriptions
            if (in_array($httpCode, [404, 410])) {
                $subscription->delete();
                Log::info('PushNotification: Fjernet utløpt subscription for bruker ' . $subscription->user_id);
            } elseif ($httpCode >= 400) {
                Log::warning('PushNotification: Feil ved sending. HTTP ' . $httpCode . ': ' . $response);
            }
        } catch (\Exception $e) {
            Log::error('PushNotification: Unntak: ' . $e->getMessage());
        }
    }

    /**
     * Encrypt payload for Web Push (aesgcm or aes128gcm).
     */
    protected function encryptPayload(string $payload, string $userPublicKey, string $userAuthToken, string $encoding): ?array
    {
        $userPublicKey = $this->base64UrlDecode($userPublicKey);
        $userAuthToken = $this->base64UrlDecode($userAuthToken);

        // Generate local key pair
        $localKey = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
        $localKeyDetails = openssl_pkey_get_details($localKey);

        // Extract raw public key (uncompressed point, 65 bytes)
        $localPublicKey = $this->extractPublicKeyRaw($localKeyDetails);

        // Derive shared secret via ECDH
        $sharedSecret = $this->computeECDH($localKey, $userPublicKey);

        if (!$sharedSecret) {
            return null;
        }

        // Derive encryption key and nonce using HKDF
        if ($encoding === 'aes128gcm') {
            $ikm = $this->hkdf('sha256', $sharedSecret, 32, "WebPush: info\0" . $userPublicKey . $localPublicKey, $userAuthToken);
            $salt = random_bytes(16);
            $prk = hash_hmac('sha256', $ikm, $salt, true);
            $contentEncryptionKey = $this->hkdf('sha256', $prk, 16, "Content-Encoding: aes128gcm\0", '');
            $nonce = $this->hkdf('sha256', $prk, 12, "Content-Encoding: nonce\0", '');

            // Pad the payload
            $paddedPayload = $payload . "\x02";

            // Encrypt
            $tag = '';
            $cipherText = openssl_encrypt($paddedPayload, 'aes-128-gcm', $contentEncryptionKey, OPENSSL_RAW_DATA, $nonce, $tag);

            // Build the content body: salt(16) + rs(4) + idlen(1) + keyid(65) + ciphertext + tag
            $rs = pack('N', 4096);
            $idlen = chr(strlen($localPublicKey));
            $body = $salt . $rs . $idlen . $localPublicKey . $cipherText . $tag;

            return [
                'cipherText' => $body,
                'encoding' => 'aes128gcm',
            ];
        } else {
            // aesgcm encoding
            $salt = random_bytes(16);
            $authInfo = "Content-Encoding: auth\0";
            $prk = $this->hkdf('sha256', $sharedSecret, 32, $authInfo, $userAuthToken);

            $context = "P-256\0"
                . pack('n', strlen($userPublicKey)) . $userPublicKey
                . pack('n', strlen($localPublicKey)) . $localPublicKey;

            $contentEncryptionKeyInfo = "Content-Encoding: aesgcm\0" . $context;
            $nonceInfo = "Content-Encoding: nonce\0" . $context;

            $prkFinal = hash_hmac('sha256', $prk, $salt, true);
            $contentEncryptionKey = $this->hkdf('sha256', $prkFinal, 16, $contentEncryptionKeyInfo, '');
            $nonce = $this->hkdf('sha256', $prkFinal, 12, $nonceInfo, '');

            // Pad the payload
            $paddedPayload = pack('n', 0) . $payload;

            // Encrypt
            $tag = '';
            $cipherText = openssl_encrypt($paddedPayload, 'aes-128-gcm', $contentEncryptionKey, OPENSSL_RAW_DATA, $nonce, $tag);

            return [
                'cipherText' => $cipherText . $tag,
                'salt' => $this->base64UrlEncode($salt),
                'serverPublicKey' => $this->base64UrlEncode($localPublicKey),
                'encoding' => 'aesgcm',
            ];
        }
    }

    protected function extractPublicKeyRaw(array $keyDetails): string
    {
        $x = str_pad($keyDetails['ec']['x'], 32, "\0", STR_PAD_LEFT);
        $y = str_pad($keyDetails['ec']['y'], 32, "\0", STR_PAD_LEFT);
        return "\x04" . $x . $y;
    }

    protected function computeECDH($localPrivateKey, string $peerPublicKeyRaw): ?string
    {
        // Build a PEM for the peer public key
        $hexKey = bin2hex($peerPublicKeyRaw);

        // ASN.1 structure for EC public key on P-256
        $asn1Header = '3059301306072a8648ce3d020106082a8648ce3d030107034200';
        $der = hex2bin($asn1Header . $hexKey);
        $pem = "-----BEGIN PUBLIC KEY-----\n" . base64_encode($der) . "\n-----END PUBLIC KEY-----\n";

        $peerKey = openssl_pkey_get_public($pem);
        if (!$peerKey) {
            return null;
        }

        $sharedSecret = openssl_pkey_derive($localPrivateKey, $peerKey, 32);
        return $sharedSecret ?: null;
    }

    protected function vapidKeyToPem(string $privateKeyBase64Url, string $publicKeyBase64Url): string
    {
        $privateKeyRaw = $this->base64UrlDecode($privateKeyBase64Url);
        $publicKeyRaw = $this->base64UrlDecode($publicKeyBase64Url);

        // Build DER encoded EC private key for P-256
        $privateKeyHex = bin2hex($privateKeyRaw);
        $publicKeyHex = bin2hex($publicKeyRaw);

        // EC PRIVATE KEY ASN.1 structure
        $der = '30770201010420' . $privateKeyHex
            . 'a00a06082a8648ce3d030107a14403420004'
            . substr($publicKeyHex, 2); // skip 0x04 prefix

        $pem = "-----BEGIN EC PRIVATE KEY-----\n"
            . chunk_split(base64_encode(hex2bin($der)), 64, "\n")
            . "-----END EC PRIVATE KEY-----\n";

        return $pem;
    }

    protected function derToRaw(string $der): string
    {
        // Parse DER SEQUENCE containing two INTEGERs
        $pos = 2; // skip SEQUENCE tag and length
        if (ord($der[1]) & 0x80) {
            $pos = 2 + (ord($der[1]) & 0x7f);
        }

        // First INTEGER (r)
        $pos++; // skip INTEGER tag
        $rLen = ord($der[$pos]);
        $pos++;
        $r = substr($der, $pos, $rLen);
        $pos += $rLen;

        // Second INTEGER (s)
        $pos++; // skip INTEGER tag
        $sLen = ord($der[$pos]);
        $pos++;
        $s = substr($der, $pos, $sLen);

        // Pad/trim to 32 bytes each
        $r = str_pad(ltrim($r, "\x00"), 32, "\x00", STR_PAD_LEFT);
        $s = str_pad(ltrim($s, "\x00"), 32, "\x00", STR_PAD_LEFT);

        return $r . $s;
    }

    protected function hkdf(string $algo, string $ikm, int $length, string $info, string $salt): string
    {
        if (empty($salt)) {
            $salt = str_repeat("\0", 32);
        }
        $prk = hash_hmac($algo, $ikm, $salt, true);
        $t = '';
        $output = '';
        $counter = 1;
        while (strlen($output) < $length) {
            $t = hash_hmac($algo, $t . $info . chr($counter), $prk, true);
            $output .= $t;
            $counter++;
        }
        return substr($output, 0, $length);
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
