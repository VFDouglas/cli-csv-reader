<?php

function env(string $variable, mixed $default = null): mixed
{
    $envFile = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($envFile as $line) {
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, null);

        $key   = trim($key);
        $value = trim($value ?? '');

        if ($key === $variable) {
            if (
                (str_starts_with($value, "'") && str_ends_with($value, "'")) ||
                (str_starts_with($value, '"') && str_ends_with($value, '"'))
            ) {
                return substr($value, 1, -1);
            }

            return $value;
        }
    }

    return $default;
}
