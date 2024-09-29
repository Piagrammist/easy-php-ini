<?php

$input = dirname(__DIR__);
$output = __DIR__ . '/easy-ini.phar';
@unlink($output);

$phar = new Phar($output, 0, $output = basename($output));
$phar->buildFromDirectory($input, '~^((?!\.vscode|\.idea|scripts|tests).)*\.(php|exe|json|lock)$~i');
$phar->setStub(<<<EOS
<?php

if (PHP_MAJOR_VERSION !== 8 || PHP_MINOR_VERSION < 2) {
    die("EasyIni requires at least php8.2".PHP_EOL);
}

Phar::interceptFileFuncs();
Phar::mapPhar('$output');

return require_once 'phar://$output/vendor/autoload.php';

__HALT_COMPILER(); ?>
EOS);
