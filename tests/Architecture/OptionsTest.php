<?php

arch('Option classes must have `Options` suffix')
    ->expect('\EasyIni\Options')
    ->toHaveSuffix('Options');
