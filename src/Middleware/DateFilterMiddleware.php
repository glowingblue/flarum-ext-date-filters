<?php

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

            switch ($params['date']) {
                case 'today':
                    $start = $now->toDateString();
                    $end   = $now->addDay()->toDateString();
                    $params['filter']['created'] = $start.'..'.$end;
                    break;

                case 'yesterday':
                    $start = $now->subDay()->toDateString();
                    $end   = $now->toDateString();
                    $params['filter']['created'] = $start.'..'.$end;
                    break;

                case 'past-7-days':
                    $start = $now->subDays(6)->toDateString();
                    $end   = $now->addDay()->toDateString();
                    $params['filter']['created'] = $start.'..'.$end;
                    break;

                case 'past-30-days':
                    $start = $now->subDays(29)->toDateString();
                    $end   = $now->addDay()->toDateString();
                    $params['filter']['created'] = $start.'..'.$end;
                    break;

                case 'past-365-days':
                    $start = $now->subDays(364)->toDateString();
                    $end   = $now->addDay()->toDateString();
                    $params['filter']['created'] = $start.'..'.$end;
                    break;
            }

            unset($params['date']);

            $request = $request->withQueryParams($params);
        }

        return $handler->handle($request);
    }
}