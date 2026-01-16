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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DateFilterMiddleware implements MiddlewareInterface
{
    private const TZ = 'UTC';
    private const FORMAT = 'Y-m-d';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();

        if (($params['date'] ?? null) && !isset($params['filter']['created'])) {
            $now = CarbonImmutable::now(self::TZ)->startOfDay();

            $dateKey = $params['date'];

            $dateFilters = [
                'today'         => fn ($n) => [$n->toDateString(), $n->addDay()->toDateString()],
                'yesterday'     => fn ($n) => [$n->subDay()->toDateString(), $n->toDateString()],
                'past-7-days'   => fn ($n) => [$n->subDays(6)->toDateString(), $n->addDay()->toDateString()],
                'past-30-days'  => fn ($n) => [$n->subDays(29)->toDateString(), $n->addDay()->toDateString()],
                'past-365-days' => fn ($n) => [$n->subDays(364)->toDateString(), $n->addDay()->toDateString()],
            ];

            if (array_key_exists($dateKey, $dateFilters)) {
                [$start, $end] = $dateFilters[$dateKey]($now);
                $params['filter']['created'] = "$start..$end";
            }

            if ($dateKey === 'custom') {
                $this->applyCustomRange($params);
            } elseif (array_key_exists($dateKey, $dateFilters)) {
                [$start, $endExclusive] = $dateFilters[$dateKey]($now);
                $params['filter']['created'] = "{$start}..{$endExclusive}";
            }

            unset($params['date'], $params['start'], $params['end']);

            $request = $request->withQueryParams($params);
        }

        return $handler->handle($request);
    }

    private function applyCustomRange(array &$params): void
    {
        $startRaw = $params['start'] ?? null;
        $endRaw = $params['end'] ?? null;

        if (!is_string($startRaw) || !is_string($endRaw)) {
            return;
        }

        $start = $this->parseDate($startRaw);
        $end = $this->parseDate($endRaw);

        if (!$start || !$end) {
            return;
        }

        if ($end->lessThan($start)) {
            return;
        }

        $endExclusive = $end->addDay();

        $params['filter']['created'] = $start->toDateString().'..'.$endExclusive->toDateString();
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
}
