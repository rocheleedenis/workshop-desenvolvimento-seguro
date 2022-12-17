<?php

// representa a secret do QRcode
// foi compartilhado do server para o usuário
$secretKey = "388384019ae9f3a34e92d1eedc8cf504c9e879ee";

// token gerado pelo Google autenthicator no celular do usuário
$codCliente = hash_hmac('sha256', date("Y/m/d h:i"), $secretKey);

// deve-se informar este token para o server
var_dump($codCliente);