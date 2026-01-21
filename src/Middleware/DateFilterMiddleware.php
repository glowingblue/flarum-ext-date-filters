<?php

/*
 * This file is part of glowingblue/date-filters.
 *
 * Copyright (c) Glowing Blue AG.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace GlowingBlue\DateFilters\Middleware;

use Carbon\CarbonImmutable;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Illuminate\Support\Arr;

class DateFilterMiddleware implements MiddlewareInterface
{
    private const TZ = 'UTC';
    private const FORMAT = 'Y-m-d';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();

        // if (!$this->isDiscussionListPath($request)) {
        //     return $handler->handle($request);
        // }

        if (! $this->shouldApplyDateFilter($params)) {
            return $handler->handle($request);
        }

        $dateKey = Arr::pull($params, 'date');
        Arr::pull($params, 'start');
        Arr::pull($params, 'end');

        $createdValue = $this->createdValueFromDateKey($dateKey, $params);

        if (! $createdValue) {
            return $handler->handle($request->withQueryParams($params));
        }

        $params = $this->applyCreatedFilter($params, $createdValue);

        $params = $this->applyCreatedTokenToSearch($params, $createdValue);

        return $handler->handle($request->withQueryParams($params));
    }

    private function shouldApplyDateFilter(array $params): bool
    {
        return isset($params['date']) && $params['date'] !== '' && ! isset($params['filter']['created']);
    }

    private function createdValueFromDateKey(?string $dateKey, array $params): ?string
    {
        if (! is_string($dateKey) || $dateKey === '') {
            return null;
        }

        if ($dateKey === 'custom') {
            return $this->customRangeValue($params);
        }

        $now = CarbonImmutable::now(self::TZ)->startOfDay();

        $ranges = [
            'today'         => fn (CarbonImmutable $n) => [$n, $n->addDay()],
            'yesterday'     => fn (CarbonImmutable $n) => [$n->subDay(), $n],
            'past-7-days'   => fn (CarbonImmutable $n) => [$n->subDays(6), $n->addDay()],
            'past-30-days'  => fn (CarbonImmutable $n) => [$n->subDays(29), $n->addDay()],
            'past-365-days' => fn (CarbonImmutable $n) => [$n->subDays(364), $n->addDay()],
        ];

        if (! isset($ranges[$dateKey])) {
            return null;
        }

        [$start, $endExclusive] = $ranges[$dateKey]($now);

        return $start->toDateString().'..'.$endExclusive->toDateString();
    }

    private function applyCreatedFilter(array $params, string $createdValue): array
    {
        $params['filter'] = array_merge($params['filter'] ?? [], [
            'created' => $createdValue,
        ]);

        return $params;
    }

    private function applyCreatedTokenToSearch(array $params, string $createdValue): array
    {
        $token = 'created:'.$createdValue;

        if (isset($params['q']) && $params['q'] !== '') {
            $params['q'] = trim($params['q'].' '.$token);
        }

        if (isset($params['filter']['q']) && $params['filter']['q'] !== '') {
            $params['filter']['q'] = trim($params['filter']['q'].' '.$token);
        }

        return $params;
    }

    private function customRangeValue(array $params): ?string
    {
        $startRaw = $params['start'] ?? null;
        $endRaw = $params['end'] ?? null;

        if (!is_string($startRaw) || !is_string($endRaw)) {
            return null;
        }

        $start = $this->parseDate($startRaw);
        $end = $this->parseDate($endRaw);

        if (!$start || !$end) {
            return null;
        }

        if ($end->lessThan($start)) {
            return null;
        }

        $endExclusive = $end->addDay();

        return $start->toDateString().'..'.$endExclusive->toDateString();
    }

    private function parseDate(string $value): ?CarbonImmutable
    {
        $date = CarbonImmutable::createFromFormat(self::FORMAT, $value, self::TZ);

        if (!$date) {
            return null;
        }

        if ($date->format(self::FORMAT) !== $value) {
            return null;
        }

        return $date->startOfDay();
    }

    private function isDiscussionListPath(ServerRequestInterface $request): bool
    {
        $path = $request->getAttribute('originalUri')->getPath();

        // Check for the 'index' route (showing all discussions)
        $settings = resolve(SettingsRepositoryInterface::class);
        $defaultRoute = $settings->get('default_route');

        if ($defaultRoute === '/all') {
            if ($path === '/') {
                return true;
            }
        } elseif ($path === '/all') {
            return true;
        }

        // Check for the 'tag' route (tag page)
        if (substr($path, 0, 2) === '/t') {
            return true;
        }

        return false;
    }
}
