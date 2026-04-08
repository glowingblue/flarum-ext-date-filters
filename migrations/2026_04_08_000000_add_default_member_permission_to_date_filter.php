<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'viewDateFilter' => Group::MODERATOR_ID,
]);
