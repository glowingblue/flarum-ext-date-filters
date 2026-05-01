<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\DateFilters\Tests\integration\forum;

use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ForumTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('glowingblue-date-filters');
    }

    #[Test]
    public function extension_boots_and_serializes()
    {
        $response = $this->send($this->request('GET', '/'));

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string) $response->getBody();

        $this->assertStringStartsWith('<!doctype html>', $body);
        $this->assertStringContainsString('</html>', $body);
    }
}
