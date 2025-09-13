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

/**
 * @param array $command Each command should be an array item. Ex.: ['vendor/bin/phinx', 'migrate']
 * @return void
 */
function runShellScript(array $command): void
{
    $cmd = implode(' ', array_map('escapeshellarg', $command));
    exec($cmd . ' 2>&1', $output, $exitCode);

    if ($exitCode !== 0) {
        fwrite(STDERR, "❌ Failed running: $cmd\n" . implode("\n", $output) . "\n");
        exit(1);
    }

    echo "✅ " . implode(' ', $command) . "\n";
}
