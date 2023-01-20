<?php

require __DIR__ . '/vendor/autoload.php';

class Crypt
{
    const TAG_LENGTH = 16;
    const ALGO = 'AES-256-GCM';
    const KEY = 'DO6u00AMMzH4QYbDFCDTWDI5CKyM0JLUmmDhgUpcQEM=';
    const KEY_SOURCE = 'jwe.key';

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

    public function decryptJWT(string $jwt)
    {
        $brokeJwt = $this->breakJwt($jwt);

        $decrypedKey = $this->decryptKeyWithRSA($brokeJwt['secretKey'], self::KEY_SOURCE);
    }

    public function breakJwt(string $jwt)
    {
        $jwtBroked = explode('.', $jwt);

        return [
            'header' => $jwtBroked[0],
            'secretKey' => $jwtBroked[1],
            'nonce' => $jwtBroked[2],
            'payload' => $jwtBroked[3],
            'tag' => $jwtBroked[4],
        ];
    }

    public function decryptKeyWithRSA(string $encryptedKey, $keySource)
    {
        $privateKey = file_get_contents($keySource);

        $private = PublicKeyLoader::load($privateKey)
            ->withPadding(RSA::ENCRYPTION_OAEP);

        return $private->decrypt($jwtBroked['secretKey']);
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

$jwtEncrypted = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSU0EtT0FFUC0yNTYiLCJlbmMiOiJBMjU2R0NNIn0.BDgqwgsI39ivg7gMRtohMK-lK3HYAuijDQCOmDUAExGStanMQWz_6D8DTqbo3AqdSln0zYf6P8OK218XJXajTxXygAwjAlUbm_UcUlFrlJGBzdWu6cJNm_UQCnpPWFrBD_JHExyV0XhKGHJPUYqxNz111JiL0lQpkcj_YjM6hKoorNyegRpSfW15HTS0a2H0BFppWjcNvuZyQS6A9P46y-9U9MGSfsWEoKTVx5cszLqJMybVh96E_5VpteoJogjUkOmlrW3U3p-zR2oJQKYxqkFcLo4O2DeERJ2zGQa-0oqlgD3CGXXCDPoOe4ctysT4Uam7TlYaSWsBpXz7xfiGcSeKwPz9VhihJUAUM4beTlDSWPERXSelky6CTePcJJZYbQTEvBl6CfquxP-I2glCW_SYNO-Y1jRMoysCCAWmTsuJGbGllneJZpAU6xcj3GJXe6ThrHS3U0b1N4tGOY8gj07M_yr_l3LLB3dAzNuT81mhm7567aJjlClXI1EJnNQzcQvB45JLA-MinodO-y2btPBPTjgEtJV8fsHYRPErghI5XjxETWL1fxQWHqymwHHSzUwbdiFq9odfmF5mZVFuBJ72RPHUOm5oYJTkLU8qxF6KsoszsmIfhRAW2wkNSkUPB2ZGfM0mzJ8L3atq38LZ63LBdXVVk6dtW3HItjjqe1M.ctc4v65ALn9IVUgp.JSQQFdKqdQfsE1EzCl5O5dDiUxfbCuKvrvucro_iwm6N_op331qZ96dAoxsQrUE5YIBmOTHc1Iv8jqIicGTgfBNFkrqT6xl8hXya9j4EeBnnCtFTc6F8js83xrvAvmGMo0ws55R9gp7FfAND.56zXexE0V2MMzreX6nlcTw';

$crypt->breakJwt($jwtEncrypted, );