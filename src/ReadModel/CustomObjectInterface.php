<?php
declare(strict_types=1);

namespace App\ReadModel;

interface CustomObjectInterface
{
    public static function fromArray(array $data = []): object;
}