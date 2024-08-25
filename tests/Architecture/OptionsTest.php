<?php

arch('Option classes must have `Options` suffix')
    ->expect('\EasyIni\Options')
    ->classes()
    ->toHaveSuffix('Options');


arch('Option classes must be final')
    ->expect('\EasyIni\Options')
    ->classes()
    ->toBeFinal();
