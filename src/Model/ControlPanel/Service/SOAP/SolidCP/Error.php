<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service\SOAP\SolidCP;

final class Error
{
    public static function getFriendlyError(int $code): string
    {
        $errors = [
            -100 => 'Username not available, already in use',
            -101 => 'Username not found, invalid username',
            -102 => 'User\'s account has child accounts',
            -300 => 'Hosting package could not be found',
            -301 => 'Hosting package has child hosting spaces',
            -501 => 'The sub-domain belongs to an existing hosting space that does not allow sub-domains to be created',
            -502 => 'The domain or sub-domain exists in another hosting space / user account',
            -511 => 'Instant alias is enabled, but not configured',
            -601 => 'The website already exists on the target hosting space or server',
            -700 => 'The email domain already exists on the target hosting space or server',
            -1100 => 'User already exists',
        ];

        // Find the error and return it, else a general error will do!
        if (array_key_exists($code, $errors)) {
            return $errors[$code];
        } else {
            return "An unknown error occurred (Code: $code). Please reference SolidCP BusinessErrorCodes for further information";
        }
    }
}