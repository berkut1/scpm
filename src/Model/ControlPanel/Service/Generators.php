<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\Service;

final class Generators
{
    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateMsVirtualMacAddress(): string
    {
        $ms_mac_prefix = "00155D"; // IEEE prefix of MS MAC addresses
        $values = [
            "0", "1", "2", "3", "4", "5", "6", "7",
            "8", "9", "A", "B", "C", "D", "E", "F",
        ];
        $valuesCount = count($values);
        $mac = $ms_mac_prefix;
        for ($i = 0; $i < 3; $i++) {
            $mac .= $values[random_int(0, $valuesCount - 1)] . $values[random_int(0, $valuesCount - 1)];
        }

        return $mac;
    }
}