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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();

        if (($params['date'] ?? null) && !isset($params['filter']['created'])) {
            $tz = 'UTC';
            $now = CarbonImmutable::now($tz)->startOfDay();

            $start = null;
            $end = null;

            switch ($params['date']) {
                case 'today':
                    $start = $now->toDateString();
                    $end = $now->addDay()->toDateString();
                    break;

                case 'yesterday':
                    $start = $now->subDay()->toDateString();
                    $end = $now->toDateString();
                    break;

                case 'past-7-days':
                    $start = $now->subDays(6)->toDateString();
                    $end = $now->addDay()->toDateString();
                    break;

                case 'past-30-days':
                    $start = $now->subDays(29)->toDateString();
                    $end = $now->addDay()->toDateString();
                    break;

                case 'past-365-days':
                    $start = $now->subDays(364)->toDateString();
                    $end = $now->addDay()->toDateString();
                    break;
            }

            if (isset($start) && isset($end)) {
                $params['filter']['created'] = $start.'..'.$end;
            }

            unset($params['date']);

            $request = $request->withQueryParams($params);
        }

        return $handler->handle($request);
    }
}
