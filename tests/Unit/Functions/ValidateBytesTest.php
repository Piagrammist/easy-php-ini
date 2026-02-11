<?php

test('validateBytes()', function (string|int $bytes, bool $truth) {
    expect(\EasyIni\validateBytes($bytes))->toBe($truth);
})
    ->with([
        [1234, true],
        [-123, false],
        [0, true],
        ['-1024k', false],
        ['1024k', true],
        ['256M', true],
        ['5G', true],
    ]);
