<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\DateFilters\Api;

use Flarum\Api\Context;
use Flarum\Api\Schema;

class AddForumAttributes
{
    public function __invoke(): array
    {
        return [
            Schema\Boolean::make('canViewDateFilter')
                ->get(fn ($model, Context $context): bool => $context->getActor()->can('viewDateFilter')),
        ];
    }
}
