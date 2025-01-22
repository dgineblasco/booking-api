<?php

declare(strict_types=1);

namespace App\Shared\Application\Response;

interface Response
{
    public function toArray(): array;
}
