<?php

namespace App\Models\Concerns;

use App\Support\Avatar;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasAvatar
{
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn (?string $value): string => Avatar::url($value, $this->name));
    }
}
