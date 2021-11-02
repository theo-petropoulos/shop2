<?php

$algo = 'aes-256-ctr';
$iv   = openssl_random_pseudo_bytes(openssl_cipher_iv_length($algo));
$key  = openssl_random_pseudo_bytes(32);
$data = 'ceci est un test';
$ciphertext = urlencode(openssl_encrypt(
    $data,
    $algo,
    $key,
    OPENSSL_ZERO_PADDING,
    $iv
));

echo $data . " = " . $ciphertext . "<br>";


$plaintext = openssl_decrypt(
    urldecode($ciphertext),
    $algo,
    $key,
    OPENSSL_ZERO_PADDING,
    $iv
);

echo $plaintext;