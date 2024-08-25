<?php

arch('Processor classes must have `Processor` suffix')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toHaveSuffix('Processor');

arch('Processor classes must be final')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toBeFinal();

arch('Processor classes must have a `process()` method')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toHaveMethod('process');
