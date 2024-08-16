<?php

$input = realpath(__DIR__.'/../');
$output = __DIR__.'/../easy-ini.phar';
@unlink($output);

$phar = new Phar($output, 0, $output = basename($output));
$phar->buildFromDirectory($input, '~^((?!scripts).)*\.(php|exe|json)$~i');
$phar->setStub(<<<EOS
<?php

if (PHP_MAJOR_VERSION !== 8 || PHP_MINOR_VERSION < 1) {
    die("EasyIni requires at least php8.1".PHP_EOL);
}

Phar::interceptFileFuncs();
Phar::mapPhar('$output');

return require_once 'phar://$output/vendor/autoload.php';

__HALT_COMPILER(); ?>
EOS);
