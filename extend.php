<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\DateFilters;

use Flarum\Api\Resource\ForumResource;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->jsDirectory(__DIR__.'/js/dist/forum')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->jsDirectory(__DIR__.'/js/dist/admin')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\ApiResource(ForumResource::class))
        ->fields(Api\AddForumAttributes::class),

    (new Extend\Middleware('forum'))
        ->add(Middleware\DateFilterMiddleware::class),
];
