<?php

test('camelToSnake()', function (string $camel, string $snake) {
    expect(\EasyIni\camelToSnake($camel))->toBe($snake);
})
    ->with([
        ['easy', 'easy'],
        ['simpleTest', 'simple_test'],
        ['AString', 'a_string'],
        ['Some3Numbers234', 'some3_numbers234'],
    ]);
