<?php

uses()->group('arch-global');

arch('project must use strict types')
    ->expect('\EasyIni')
    ->toUseStrictTypes();
