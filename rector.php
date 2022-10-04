<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
      __DIR__ . '/src',
      __DIR__ . '/tests',
    ]);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    $rectorConfig->sets([
      DowngradeLevelSetList::DOWN_TO_PHP_73
    ]);
};
