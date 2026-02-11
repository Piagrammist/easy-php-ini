<?php

arch('Options must be classes')
    ->expect('\EasyIni\Options')
    ->toBeClasses();

arch('Option classes must have `Options` suffix')
    ->expect('\EasyIni\Options')
    ->toHaveSuffix('Options');

arch('Option classes must be `final`')
    ->expect('\EasyIni\Options')
    ->toBeFinal();
