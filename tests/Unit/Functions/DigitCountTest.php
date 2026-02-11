<?php

test('digitCount()', function (int $number) {
    expect(\EasyIni\digitCount($number))->toBe(strlen((string)abs($number)));
})
    ->with([
        1234,
        -123,
        0,
        PHP_INT_MAX,
    ]);
