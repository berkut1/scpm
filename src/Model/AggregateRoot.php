<?php
declare(strict_types=1);

namespace App\Model;

interface AggregateRoot //https://romaricdrigon.github.io/2019/08/09/domain-events
{
    public function releaseEvents(): array;
}
