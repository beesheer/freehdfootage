<?php
class Util_RandomStringGenerator
{
    const lenght = 5;
    public static function generate($client_id)
    {
        $token = bin2hex(openssl_random_pseudo_bytes(self::lenght)). hash('md5', $client_id.time());
        return $token;
    }
}