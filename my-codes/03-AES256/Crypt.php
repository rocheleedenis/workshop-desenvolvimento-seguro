<?php

class Crypt
{
    const TAG_LENGTH = 16;
    const ALGO = 'AES-256-GCM';
    const KEY = 'DO6u00AMMzH4QYbDFCDTWDI5CKyM0JLUmmDhgUpcQEM=';

    public function encrypt(string $plaintext)
    {
        $tag = null;
        $iv = random_bytes($this->getIvLength());
        $cypherText = openssl_encrypt(
            $plaintext,
            self::ALGO,
            $this->getKey(),
            $this->getOpenSslOptions(),
            $iv,
            $tag,
            "",
            self::TAG_LENGTH
        );

        return base64_encode($iv . $tag . $cypherText);
    }

    public function decrypt(string $message)
    {
        $decode = base64_decode($message);
        $ivLength = $this->getIvLength();
        $iv = substr($decode, 0, $ivLength);
        $tag = substr($decode, $ivLength, self::TAG_LENGTH);
        $cypherText = substr($decode, $ivLength + self::TAG_LENGTH);

        return openssl_decrypt(
            $cypherText,
            self::ALGO,
            $this->getKey(),
            $this->getOpenSslOptions(),
            $iv,
            $tag
        );
    }

    private function getKey()
    {
        return base64_decode(self::KEY);
    }

    private function getOpenSslOptions()
    {
        return OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;
    }

    private function getIvLength()
    {
        return openssl_cipher_iv_length(self::ALGO);
    }
}

$crypt = new Crypt();
$crypted = $crypt->encrypt('xablau');
$original = $crypt->decrypt($crypted);

var_dump('crypted: ' . $crypted);
var_dump('decrypted: ' . $original);