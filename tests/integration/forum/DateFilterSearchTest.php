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

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DateFilterSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('glowingblue-date-filters');

        $this->database()->rollBack();

        // MySQL and MariaDB only index FULLTEXT columns for committed rows, so
        // the searchable data goes in outside the test transaction and is
        // removed in tearDown(), as core's own fulltext tests do.
        $discussions = [];
        $posts = [];

        foreach ([30, 20, 6, 1, 0] as $id => $daysAgo) {
            $id++;
            $createdAt = Carbon::now()->subDays($daysAgo)->toDateTimeString();

            $discussions[] = ['id' => $id, 'title' => "lightsail discussion $id", 'created_at' => $createdAt, 'user_id' => 1, 'comment_count' => 1];
            $posts[] = ['id' => $id, 'discussion_id' => $id, 'number' => 1, 'created_at' => $createdAt, 'user_id' => 1, 'content' => '<t><p>lightsail</p></t>'];
        }

        $discussions[] = ['id' => 6, 'title' => 'unrelated discussion', 'created_at' => Carbon::now()->toDateTimeString(), 'user_id' => 1, 'comment_count' => 1];
        $posts[] = ['id' => 6, 'discussion_id' => 6, 'number' => 1, 'created_at' => Carbon::now()->toDateTimeString(), 'user_id' => 1, 'content' => '<t><p>unrelated</p></t>'];

        $this->database()->table('discussions')->insert($this->rowsThroughFactory(Discussion::class, $discussions));
        $this->database()->table('posts')->insert($this->rowsThroughFactory(Post::class, $posts));

        // Each discussion's first post is its own id; set once the posts exist,
        // since first_post_id is a foreign key.
        foreach ($discussions as $discussion) {
            $this->database()->table('discussions')->where('id', $discussion['id'])->update(['first_post_id' => $discussion['id']]);
        }

        // Rolling back ended the transaction that ::app() started.
        $this->database()->beginTransaction();

        $this->populateDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->database()->table('discussions')->delete();
        $this->database()->table('posts')->delete();
    }

    #[Test]
    public function can_combine_date_preset_with_search(): void
    {
        $this->assertSame([3, 4, 5], $this->discussionIdsOnPage([
            'q'    => 'lightsail',
            'date' => 'past-7-days',
        ]));
    }

    #[Test]
    public function can_combine_custom_date_range_with_search(): void
    {
        $this->assertSame([2, 3], $this->discussionIdsOnPage([
            'q'     => 'lightsail',
            'date'  => 'custom',
            'start' => Carbon::now()->subDays(25)->toDateString(),
            'end'   => Carbon::now()->subDays(3)->toDateString(),
        ]));
    }

    #[Test]
    public function search_without_date_filter_is_unchanged(): void
    {
        $this->assertSame([1, 2, 3, 4, 5], $this->discussionIdsOnPage(['q' => 'lightsail']));
    }

    /**
     * @return int[]
     */
    private function discussionIdsOnPage(array $queryParams): array
    {
        $response = $this->send(
            $this->request('GET', '/')->withQueryParams($queryParams)
        );

        $this->assertEquals(200, $response->getStatusCode());

        if (!preg_match('/<script[^>]+id="flarum-json-payload"[^>]*>(?<json>.*?)<\/script>/s', (string) $response->getBody(), $m)) {
            $this->fail('Could not find flarum-json-payload script tag.');
        }

        $payload = json_decode(trim($m['json']), true);

        $ids = array_map(fn ($resource) => (int) $resource['id'], $payload['apiDocument']['data'] ?? []);
        sort($ids);

        return $ids;
    }
}
