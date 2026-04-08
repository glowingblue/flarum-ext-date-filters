<?php

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
