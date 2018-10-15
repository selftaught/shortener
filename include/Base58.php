<?php

/**
 * Base58 encoding / decoding helper class.
 *
 * @resource url https://en.bitcoin.it/wiki/Base58Check_encoding#Base58_symbol_chart
 */
class Base58
{
    /**
     *
     */
    public static $charset = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * Encode an integer to Base58.
     *
     * @param  int $integer integer value to encoded
     * @return string $encoded
     */
    public static function encode($integer)
    {
        $encoded   = '';
        $cs_strlen = strlen(self::$charset);

        while ($integer >= $cs_strlen) {
            $divided = floor($integer / $cs_strlen);
            $modulus = ($integer - ($cs_strlen * $divided));
            $encoded = self::$charset{(int)$modulus} . $encoded;
            $integer = $divided;
        }

        if ($integer) {
            $encoded = self::$charset{(int)$integer} . $encoded;
        }

        return $encoded;
    }

    /**
     * Decodes a Base58::encode'd value.
     *
     * @param  string $encoded encoded value to decode
     * @return int    $integer
     */
    public static function decode($encoded)
    {
        $integer   = 0;
        $cs_strlen = strlen(self::$charset);

        for ($i = strlen($encoded) - 1, $j = 1; $i >= 0; $i--, $j *= $cs_strlen) {
            $integer += $j * strpos(self::$charset, $encoded{$i});
        }

        return $integer;
    }
}
