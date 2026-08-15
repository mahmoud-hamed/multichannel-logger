<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Logging;

use Monolog\Level;
use Monolog\Logger;

abstract class WebhookLoggerFactory
{
    /** @var array<string, Level> */
    private const LEVEL_ALIASES = [
        'debug' => Level::Debug,
        'info' => Level::Info,
        'notice' => Level::Notice,
        'warning' => Level::Warning,
        'error' => Level::Error,
        'critical' => Level::Critical,
        'alert' => Level::Alert,
        'emergency' => Level::Emergency,
    ];

    /**
     * @param  array<string, mixed>  $config
     */
    abstract public function __invoke(array $config): Logger;

    /**
     * @param  array<string, mixed>  $config
     */
    protected function level(array $config, Level $default = Level::Debug): Level
    {
        $level = $config['level'] ?? null;

        if ($level instanceof Level) {
            return $level;
        }

        if (is_string($level)) {
            return self::LEVEL_ALIASES[strtolower($level)] ?? $default;
        }

        if (is_int($level)) {
            return Level::from($level);
        }

        return $default;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function intValue(array $config, string $key, int $default): int
    {
        $value = $config[$key] ?? $default;

        return is_int($value) ? $value : (int) $value;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function boolValue(array $config, string $key, bool $default): bool
    {
        $value = $config[$key] ?? $default;

        if (is_bool($value)) {
            return $value;
        }

        $filtered = is_string($value) || is_int($value)
            ? filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        if (is_bool($filtered)) {
            return $filtered;
        }

        return $default;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function stringValue(array $config, string $key, ?string $default = null): ?string
    {
        $value = $config[$key] ?? $default;

        return is_string($value) ? $value : $default;
    }
}
