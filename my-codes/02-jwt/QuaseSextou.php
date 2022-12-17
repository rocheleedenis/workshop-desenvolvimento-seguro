<?php

class QuaseSextou
{
    protected const ALGORITHMS = [
        'SHA-256' => 'sha256',
        'SHA-384' => 'sha384',
        'SHA-512' => 'sha512',
    ];

    private const SECRET = "secret";

    public function sign(string $algo, $message) : string
    {
        $encoded = base64_encode(json_encode($message));

        $jwtAlgo = self::ALGORITIMOS[$algo];

        $header = base64_encode(json_encode([
            'alg' => $jwtAlgo,
            'typ' => 'JWT',
        ]));

        return "$header.$encoded." . base64_encode(hash_hmac($jwtAlgo, self::SECRET, "$header.$encoded"));
    }

    public function verify(string $message) : bool
    {
        $brokedMessage = explode('.', $message);

        $header = base64_decode($brokedMessage[0]);
        $header = json_decode($header, true);

        $encoded = $brokedMessage[1];
        $provided = base64_decode($brokedMessage[2]);
        $expected = hash_hmac($header['alg'], self::SECRET, "$brokedMessage[0].$encoded");

        return hash_equals($expected, $provided);
    }
}

$quaseSextou = new QuaseSextou();
// var_dump($quaseSextou->sign('Sextou!!!'));
// var_dump($quaseSextou->verify("Sextou!!!.d94307c5214b86db8ec38215613130f98c04d8b9a55ed8ccbe4b597bfbd22e4c"));

// var_dump($quaseSextou->sign('Sextou!!!'));
// var_dump($quaseSextou->verify("IlNleHRvdSEhISI=.OTI3OGU5ZjI4OTVjNDk4NzZhOWVmMTcyYzEzMDM3YjgxMGQ2M2ZiYzhlNmFlMzg5NjE1MmU3MDVmZmMzYTkxOA=="));

// var_dump($quaseSextou->sign('sha256', 'Sextou!!!'));
// var_dump($quaseSextou->verify("sha256.IlNleHRvdSEhISI=.OTI3OGU5ZjI4OTVjNDk4NzZhOWVmMTcyYzEzMDM3YjgxMGQ2M2ZiYzhlNmFlMzg5NjE1MmU3MDVmZmMzYTkxOA=="));

$quaseSextou = new QuaseSextou();
// var_dump($quaseSextou->sign('SHA-256', 'Sextou!!!'));
var_dump($quaseSextou->verify(
    "eyJhbGciOiJzaGEyNTYiLCJ0eXAiOiJKV1QifQ==.IlNleHRvdSEhISI=.YmI2OWZjNjNkOTk2OWI1MjQ0ZTkzMjg4MmZlMDY2Y2Q2ZTdkODVjMDc0YzRhOTNmNDk3MDQ3YTUxZTM1ODhkNQ=="
));