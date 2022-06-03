<?php

namespace App\Dorcas\Hub\Utilities;

/**
 * Class Gravatar
 * A Helper class to help with getting Gravatar images based on the supplied email address
 *
 * @link https://en.gravatar.com/site/implement/images/
 * @package BrassPay\BrassPay\Utilities
 */
class Gravatar
{
    const URL_HTTP = 'http://www.gravatar.com';
    const URL_HTTPS = 'https://secure.gravatar.com';
    
    const RATED_G = 'g';
    const RATED_PG = 'pg';
    const RATED_R = 'r';
    const RATED_X = 'x';
    
    const DEFAULT_IMG_404 = '404';
    const DEFAULT_IMG_BLANK = 'blank';
    const DEFAULT_IMG_IDENTICON = 'identicon';
    const DEFAULT_IMG_MM = 'mm'; # mystery-man
    const DEFAULT_IMG_MONSTERID = 'monsterid';
    const DEFAULT_IMG_RETRO = 'retro';
    const DEFAULT_IMG_WAVATAR = 'wavatar';
    
    /**
     * Creates the Gravatar URL for the provided email address.
     *
     * @param string     $email     The email address to generate the gravatar URL for
     * @param bool|false $secure    Whether to generate a HTTP or HTTPS URL
     * @param int        $width     The image width, supported range [1 - 2048]
     * @param string     $default   Specify a default image in case there is not gravatar for the email. You can either specify a URL or one of the Gravatar::DEFAULT_IMG_* constants
     * @param string     $rating    The maximum image rating to fetch. One of the Gravatar::RATED_* constants. Setting it to RATED_PG will return either an image rated PG [if available] or G
     *
     * @return string
     */
    public static function getGravatar(
        string $email = null,
        bool $secure = true,
        int $width = 400,
        string $default = self::DEFAULT_IMG_RETRO,
        string $rating = self::RATED_G
    ): string {
        $email = $email ?: 'fake@example.com';
        $width = $width > 0 && $width < 2048  ? $width : 80;
        $default = $default ?: self::DEFAULT_IMG_RETRO;
        $rating = !in_array(strtolower($rating), [self::RATED_G, self::RATED_PG, self::RATED_R, self::RATED_X], true) ?
            self::RATED_G : strtolower($rating);
        # we set the defaults
        $baseUrl = $secure ? self::URL_HTTPS : self::URL_HTTP;
        # we set the base URL
        $hash = hash('md5', strtolower(trim($email)));
        $query = ['s' => $width, 'd' => $default, 'r' => $rating];
        return $baseUrl.'/avatar/'.$hash.'?'.http_build_query($query);
    }
}
