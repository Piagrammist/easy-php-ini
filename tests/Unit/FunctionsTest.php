<?php

uses()->group('functions');

test('digitCount()', function (int $number) {
    expect(\EasyIni\digitCount($number))->toBe(strlen((string)abs($number)));
})
    ->with([
        1234,
        -123,
        0,
        PHP_INT_MAX,
    ]);


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
        ['opcache.jit_buffer_size', true],
    ]);


test('camelToSnake()', function (string $camel, string $snake) {
    expect(\EasyIni\camelToSnake($camel))->toBe($snake);
})
    ->with([
        ['easy', 'easy'],
        ['simpleTest', 'simple_test'],
        ['AString', 'a_string'],
        ['Some3Numbers234', 'some3_numbers234'],
    ]);


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
