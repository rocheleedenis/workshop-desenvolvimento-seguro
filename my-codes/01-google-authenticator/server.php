<?php

// echo bin2hex(random_bytes(20));
// representa a secret do QRcode
$secretKey = "388384019ae9f3a34e92d1eedc8cf504c9e879ee";
// var_dump($secretKey);

//simulando servidor gerando o token para comparação
$codServer = hash_hmac('sha256', date("Y/m/d h:i"), "388384019ae9f3a34e92d1eedc8cf504c9e879ee");

// cole aqui o token gerado pelo client
$codClient = "9df323e762400a123899448bcebb57e624e80a843022b51b24e90d7d64141a59";

// o token gerado pelo servidor e o gerado pelo client têm que ser o mesmo!
var_dump(hash_equals($codServer, $codClient));