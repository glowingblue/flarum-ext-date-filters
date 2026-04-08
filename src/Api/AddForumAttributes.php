<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Glowingblue\DateFilters\Api;

use Flarum\Api\Serializer\ForumSerializer;

class AddForumAttributes
{
    public function __invoke(ForumSerializer $serializer)
    {
        $user = $serializer->getActor();

        $attributes['canViewDateFilter'] = $user->can('viewDateFilter');

        return $attributes;
    }
}
