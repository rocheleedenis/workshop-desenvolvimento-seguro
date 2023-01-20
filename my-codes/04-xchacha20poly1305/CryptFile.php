<?php

class Crypt
{
    private const KEY = 'DO6u00AMMzH4QYbDFCDTWDI5CKyM0JLUmmDhgUpcQEM=';
    private const CHUNK_SIZE = 32;

    public function encryptFile(string $source, $adicionalData)
    {
        $response = sodium_crypto_secretstream_xchacha20poly1305_init_push($this->getKey());

        $handle = fopen($source, 'rb');

        file_put_contents('encrypted_file.txt', $response['header'], FILE_APPEND);

        while(feof($handle) === false)
        {
            $chunk = fread($handle, self::CHUNK_SIZE);

            $encryptedText = $this->encryptText($chunk, $adicionalData);

            file_put_contents('encrypted_file.txt', $encryptedText, FILE_APPEND);
        }

        fclose($handle);
    }

    public function encryptText(string $plaintext, $adicionalData)
    {
        $nonce = $this->getNonce();
        $cypherText = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt(
            $plaintext,
            $adicionalData,
            $nonce,
            $this->getKey()
        );

        return base64_encode($nonce . $cypherText);
    }

    public function decryptFile($source, $adicionalData)
    {
        $handle = fopen($source, 'rb');

        // ler o cabeçalho
        $header = 'cabeçalho'
        sodium_crypto_secretstream_xchacha20poly1305_init_pull($header, $this->getKey());

        while(feof($handle) === false)
        {
            $chunk = fread($handle, self::CHUNK_SIZE - SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_ABYTES);

            $decrypted = $this->decryptText($chunk, $adicionalData);
            tagpush e tag message

            file_put_contents('decrypted_file.txt', $decrypted, FILE_APPEND);
        }

        fclose($handle);
    }

    public function decryptText(string $message, $adicionalData)
    {
        $decode = base64_decode($message);
        $nonce = substr($decode, 0, $this->getNonceSize());
        $cypherText = substr($decode, $this->getNonceSize());

        $decrypted = sodium_crypto_aead_xchacha20poly1305_ietf_decrypt(
            $cypherText,
            $adicionalData,
            $nonce,
            $this->getKey()
        );

        if ($decrypted === false) {
            throw new Exception("Decryption failed!", 5);
        }

        return $decrypted;
    }

    private function getKey()
    {
        return base64_decode(self::KEY);
    }

    private function getNonce()
    {
        return random_bytes($this->getNonceSize());
    }

    private function getNonceSize()
    {
        return SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES;
    }
}

$crypt = new Crypt();
$crypted = $crypt->encryptFile('/var/04-xchacha20poly1305/index.html', 'ddd');
$decrypted = $crypt->decryptFile('/var/04-xchacha20poly1305/encrypted_file.txt', 'ddd');
