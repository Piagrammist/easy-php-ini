<?php

uses()->group('arch-processors');

arch('Processor classes must have `Processor` suffix')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toHaveSuffix('Processor');

arch('Processor classes must be `final`')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toBeFinal();

arch('Processor classes must extend `AbstractProcessor`')
    ->expect('\EasyIni\Processors')
    ->classes()
    ->toExtend('\EasyIni\Processors\AbstractProcessor');
