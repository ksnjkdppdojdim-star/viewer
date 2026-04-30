<?php
namespace Core\Support;

class Env
{
    private static array $values = [];

    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$values[$key] = $value;
            $_ENV[$key] = $value;
        }
    }

    public static function get(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            $value = self::$values[$key] ?? $default;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if (in_array($lower, ['true', 'false'], true)) {
                return $lower === 'true';
            }
            if (is_numeric($value)) {
                return str_contains($value, '.') ? (float) $value : (int) $value;
            }
        }

        return $value;
    }
}
