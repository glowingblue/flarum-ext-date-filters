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
use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateFilterMiddlewareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('glowingblue-date-filters');

        $this->prepareDatabase([
            Discussion::class => [
                ['id' => 1, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(380)->toDateTimeString(), 'user_id' => 1],
                ['id' => 2, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(366)->toDateTimeString(), 'user_id' => 1],
                ['id' => 3, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(330)->toDateTimeString(), 'user_id' => 1],
                ['id' => 4, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(300)->toDateTimeString(), 'user_id' => 1],
                ['id' => 5, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(150)->toDateTimeString(), 'user_id' => 1],
                ['id' => 6, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(32)->toDateTimeString(), 'user_id' => 1],
                ['id' => 7, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(29)->toDateTimeString(), 'user_id' => 1],
                ['id' => 8, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(15)->toDateTimeString(), 'user_id' => 1],
                ['id' => 9, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(8)->toDateTimeString(), 'user_id' => 1],
                ['id' => 10, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(6)->toDateTimeString(), 'user_id' => 1],
                ['id' => 11, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->subDays(1)->toDateTimeString(), 'user_id' => 1],
                ['id' => 12, 'title' => ' Discussion Title', 'created_at' => Carbon::now()->toDateTimeString(), 'user_id' => 1],
            ],
        ]);
    }

    public static function datePresetProvider(): array
    {
        return [
            'today'         => ['today', [12]],
            'yesterday'     => ['yesterday', [11]],
            'past-7-days'   => ['past-7-days', [10, 11, 12]],
            'past-30-days'  => ['past-30-days', [7, 8, 9, 10, 11, 12]],
            'past-365-days' => ['past-365-days', [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]],
        ];
    }

    #[Test]
    #[DataProvider('datePresetProvider')]
    public function can_filter_discussions_by_date_preset(string $preset, array $expectedDiscussionIds): void
    {
        $response = $this->send(
            $this->request('GET', '/')
                ->withQueryParams(['date' => $preset])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $html = (string) $response->getBody();
        $payload = $this->extractJsonPayload($html);

        $discussionIds = array_map(
            fn ($resource) => (int) $resource['id'],
            $payload['apiDocument']['data'] ?? []
        );

        sort($discussionIds);
        sort($expectedDiscussionIds);

        $this->assertSame($expectedDiscussionIds, $discussionIds, "Preset '{$preset}' returned unexpected discussions.");
    }

    #[Test]
    public function can_filter_discussions_by_custom_date_range(): void
    {
        $startDate = Carbon::now()->subDays(32)->toDateString();
        $endDate = Carbon::now()->subDays(6)->toDateString();

        $response = $this->send(
            $this->request('GET', '/')
                ->withQueryParams([
                    'date'  => 'custom',
                    'start' => $startDate,
                    'end'   => $endDate,
                ])
        );
        $this->assertEquals(200, $response->getStatusCode());

        $html = (string) $response->getBody();
        $payload = $this->extractJsonPayload($html);

        $discussionIds = array_map(
            fn ($resource) => (int) $resource['id'],
            $payload['apiDocument']['data'] ?? []
        );

        sort($discussionIds);

        $expectedDiscussionIds = [6, 7, 8, 9, 10];

        $this->assertSame($expectedDiscussionIds, $discussionIds, 'Custom date range returned unexpected discussions.');
    }

    private function extractJsonPayload(string $html): array
    {
        if (!preg_match(
            '/<script[^>]+id="flarum-json-payload"[^>]*>(?<json>.*?)<\/script>/s',
            $html,
            $m
        )) {
            $this->fail('Could not find flarum-json-payload script tag.');
        }

        $json = trim($m['json']);
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            $this->fail('Could not decode flarum-json-payload JSON.');
        }

        return $decoded;
    }

    #[Test]
    public function test_no_query_params_returns_valid_response(): void
    {
        $response = $this->send(
            $this->request('GET', '/')
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function unknown_date_preset_returns_all_discussions(): void
    {
        $response = $this->send(
            $this->request('GET', '/')
                ->withQueryParams(['date' => 'not-a-real-preset'])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $payload = $this->extractJsonPayload((string) $response->getBody());
        $this->assertCount(12, $payload['apiDocument']['data'] ?? []);
    }

    #[Test]
    public function custom_range_with_end_before_start_returns_all_discussions(): void
    {
        $response = $this->send(
            $this->request('GET', '/')
                ->withQueryParams([
                    'date'  => 'custom',
                    'start' => Carbon::now()->toDateString(),
                    'end'   => Carbon::now()->subDays(10)->toDateString(),
                ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $payload = $this->extractJsonPayload((string) $response->getBody());
        $this->assertCount(12, $payload['apiDocument']['data'] ?? []);
    }

    #[Test]
    public function custom_range_with_malformed_dates_returns_all_discussions(): void
    {
        $response = $this->send(
            $this->request('GET', '/')
                ->withQueryParams([
                    'date'  => 'custom',
                    'start' => 'not-a-date',
                    'end'   => 'also-not-a-date',
                ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $payload = $this->extractJsonPayload((string) $response->getBody());
        $this->assertCount(12, $payload['apiDocument']['data'] ?? []);
    }
}
