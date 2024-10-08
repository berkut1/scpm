<?php
declare(strict_types=1);

namespace App\Tests;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

final class BypassFinalHook implements BeforeTestHook
{
    #[\Override]
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}