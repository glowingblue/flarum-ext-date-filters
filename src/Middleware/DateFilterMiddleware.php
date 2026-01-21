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

class DateFilterMiddleware implements MiddlewareInterface
{
    private const TZ = 'UTC';
    private const FORMAT = 'Y-m-d';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();

        if (!$this->isDiscussionListPath($request)) {
            return $handler->handle($request);
        }

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

            $createdValue = null;

            if ($dateKey === 'custom') {
                $createdValue = $this->customRangeValue($params);
            } elseif (isset($dateFilters[$dateKey])) {
                [$start, $endExclusive] = $dateFilters[$dateKey]($now);
                $createdValue = "{$start}..{$endExclusive}";
            }

            if ($createdValue) {
                $params['filter']['created'] = $createdValue;

                $token = 'created:'.$createdValue;

                if (isset($params['q']) && $params['q'] !== '') {
                    $params['q'] .= ' '.$token;
                }

                if (isset($params['filter']['q']) && $params['filter']['q'] !== '') {
                    $params['filter']['q'] .= ' '.$token;
                }
            }

            unset($params['date'], $params['start'], $params['end']);

            return $handler->handle($request->withQueryParams($params));
        }

        return $handler->handle($request);
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
