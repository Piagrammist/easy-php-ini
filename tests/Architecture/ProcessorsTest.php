<?php

arch('Processors must be classes')
    ->expect('\EasyIni\Processors')
    ->toBeClasses();

arch('Processor classes must have `Processor` suffix')
    ->expect('\EasyIni\Processors')
    ->toHaveSuffix('Processor');

arch('Processor classes must be `final`')
    ->expect('\EasyIni\Processors')
    ->toBeFinal();

arch('Processor classes must extend `AbstractProcessor`')
    ->expect('\EasyIni\Processors')
    ->toExtend('\EasyIni\Processors\AbstractProcessor');
