<?php

test('validateSnake()', function (string $str, bool $truth) {
    expect(\EasyIni\validateSnake($str))->toBe($truth);
})
    ->with([
        ['easy', true],
        ['simple_test', true],
        ['some3_numbers234', true],
        ['AString', false],
        ['a__string', false],
        ['_string', false],
        ['opcache.jit_buffer_size', false],
    ]);
