<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Application\Config;
use NunoMaduro\PhpInsights\Domain\Metrics;
use NunoMaduro\PhpInsights\Domain\Insights;

return Config::make()
    ->setName('Farmer App')
    ->setPaths([__DIR__ . '/app'])
    ->setExclude([])
    ->setPreset('laravel')
    ->addRemoved([...Config::DEFAULT_INSIGHTS,])
    ->addAdded([])
    ->addConfig([]);

