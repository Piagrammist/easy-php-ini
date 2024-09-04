<?php declare(strict_types=1);

namespace EasyIni;

final class Lang
{
    private static array $strings = [
        'err_class_resolve' => "Could not resolve class '%s'",
        'err_env_mode'      => "Invalid environment mode '%s'",
        'err_setup'         => 'Cannot setup more than once',
        'err_key_str'       => "Invalid string key provided '%s'",
        'err_argc_match'    => "Arguments count don't match the %s",
        'err_bytes'         => '%s size must be a positive value in bytes, ' .
            "or with standard PHP data size suffixes (K, M or G) e.g. '256M'",
        'err_id_resolve'    => "%s '%s' does not exist and will be ignored!",
        'err_file_resolve'  => "File does not exist at '%s'",
        'err_ini_resolve'   => 'Could not resolve the ini path',
        'err_entry_empty'   => 'Entry name cannot be empty',
        'err_entry_snake'   => 'Entry name must be snake_case',
        'err_jit_flags'     => 'JIT flags must be a 4 digit number or one of "%s"',
        'err_win_no_ext'    => 'Extension handling is only supported on Windows. Skipping...',
        'debug_pattern'     => "PatternPair{ '%s' => '%s' }",
        'debug_entry'       => "Entry{ '%s' = %s }",
        'ini_read'          => "Using '%s' as template.",
        'ini_write'         => "Writing to '%s'.",
        'env_mode'          => 'Env mode: %s',
        'entry_add'         => 'No `%s` entry found, proceeding to add.',
        'disable_fn'        => 'Found %s function(s) to disable.',
        'disable_cls'       => 'Found %s class(es) to disable.',
        'jit_processed'     => 'JIT processed.',
        'no_x'              => 'No %s provided.',
        'count'             => 'Got %s %s.',
        'no_option'         => 'No %s option provided.',
        'option_count'      => 'Got %s %s option(s).',
        'done'              => 'Done!',
    ];

    public static function get(string $key, string ...$args): string
    {
        $str = self::$strings[strtolower($key)] ?? null;
        if ($str === null) {
            throw new \InvalidArgumentException(Lang::get('err_key_str', $key));
        }
        if (mb_substr_count($str, '%s') !== func_num_args() - 1) {
            throw new \InvalidArgumentException(Lang::get('err_argc_match', "format strings'"));
        }
        return sprintf($str, ...$args);
    }
}
