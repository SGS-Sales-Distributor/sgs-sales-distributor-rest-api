<?php

namespace App\Handlers;

use App\Models\User;
use DateTimeImmutable;
use DateTimeZone;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class JwtAuthToken
{
    protected const DEFAULT_CIPHER_ALGO = 'aes-256-cbc';

    public function generateToken(User $user): string
    {
        $secretKey = env('JWT_SECRET_KEY');

        $tokenID = base64_encode(random_bytes(16));

        $issueAt = new DateTimeImmutable(datetime: now(), timezone: new DateTimeZone('Asia/Jakarta'));

        $expiredTime = $issueAt->modify('+30 minutes')->getTimestamp();

        $request = Request::capture();

        $serverName = $request->fullUrl();

        $userData = [
            "fullname" => $user->fullname,
            "phone_number" => $user->phone,
            "email" => $user->email,
            "username" => $user->name,
            "status" => $user->status?->status ?? 'inactive',
        ];

        $payload = [
            "iss" => $serverName,
            "iat" => $issueAt->getTimestamp(),
            "exp" => $expiredTime,
            "nbf" => $issueAt->getTimestamp(),
            "jti" => $tokenID,
            "sub" => $user->number,
            "user" => $this->encryptUserData($userData),
        ];

        $jwt = JWT::encode(
            payload: $payload,
            key: $secretKey,
            alg: 'HS512',
        );

        return $jwt;
    }

    public function generateRefreshToken(User $user): string
    {
        $refreshSecretKey = env('REFRESH_SECRET_KEY');

        $tokenID = base64_encode(random_bytes(16));

        $issueAt = new DateTimeImmutable(timezone: new DateTimeZone(env('APP_TIMEZONE')));

        $expiredTime = $issueAt->modify('+20160 minutes')->getTimestamp();

        $request = Request::capture();
        
        $serverName = $request->fullUrl();

        $payload = [
            "iss" => $serverName,
            "iat" => $issueAt->getTimestamp(),
            "exp" => $expiredTime,
            "nbf" => $issueAt->getTimestamp(),
            "jti" => $tokenID,
            "sub" => $user->number,
        ];

        $refreshToken = JWT::encode(
            payload: $payload,
            key: $refreshSecretKey,
            alg: 'HS512',
        );

        return $refreshToken;
    }

    public function encryptUserData(array $data): string
    {
        $secretKey = env('ENCRYPT_SECRET_KEY');

        $vectorIV = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this::DEFAULT_CIPHER_ALGO));

        $encryptData = base64_encode($vectorIV . openssl_encrypt(json_encode($data), $this::DEFAULT_CIPHER_ALGO, $secretKey, 0, $vectorIV));

        return $encryptData;
    }

    public function decryptUserData(string $encryptedData): array
    {
        $data = base64_decode($encryptedData);

        $vectorIV = substr($data, 0, openssl_cipher_iv_length($this::DEFAULT_CIPHER_ALGO));

        $encryptData = substr($data, openssl_cipher_iv_length($this::DEFAULT_CIPHER_ALGO));

        $decryptData = openssl_decrypt($encryptData, $this::DEFAULT_CIPHER_ALGO, env('ENCRYPT_SECRET_KEY'), 0, $vectorIV);

        $decryptedArr = json_decode($decryptData, true);

        return $decryptedArr;
    }
}