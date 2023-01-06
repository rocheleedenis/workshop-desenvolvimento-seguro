<?php

class Crypt
{
    const KEY = 'DO6u00AMMzH4QYbDFCDTWDI5CKyM0JLUmmDhgUpcQEM=';

    public function encrypt(string $plaintext, $adicionalData)
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

    public function decrypt(string $message, $adicionalData)
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
$crypted = $crypt->encrypt('azul marinho', 'ddd');
$original = $crypt->decrypt($crypted, 'ddd');

var_dump('crypted: ' . $crypted);
var_dump($original);