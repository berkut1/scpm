<?php

declare(strict_types=1);

namespace App\ReadModel\User\Filter;

final class Filter
{
    public ?string $login = null;
    public ?string $role = null;
    public ?string $status = null;
}
