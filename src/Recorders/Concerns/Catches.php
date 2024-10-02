<?php

declare(strict_types=1);

namespace ZaimeaLabs\Pulse\Analytics\Recorders\Concerns;

use Illuminate\Support\Facades\Config;

trait Catches
{
    /**
     * Determine if the given value should be catched.
     */
    protected function shouldCatch(string $value): bool
    {
        // @phpstan-ignore argument.templateType, argument.templateType
        return collect(Config::get('pulse.recorders.'.static::class.'.catch', []))
            ->contains(fn (string $pattern) => preg_match($pattern, $value));
    }
}
