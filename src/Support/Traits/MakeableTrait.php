<?php

namespace Support\Traits;

trait MakeableTrait
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}
