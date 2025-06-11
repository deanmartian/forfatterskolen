<?php

namespace App\Traits;

trait EnumsWithOptions
{
    /**
     * Get options suitable for HTML select inputs or similar.
     * Returns ['label' => value, 'value' => value].
     */
    public static function toOptions(): array
    {
        $constants = self::getConstants();
        $options = [];

        foreach ($constants as $name => $value) {
            $options[] = [
                'label' => $name,
                'value' => $value,
            ];
        }

        return $options;
    }

    /**
     * Get all constant values as an array.
     */
    public static function values(): array
    {
        return array_values(self::getConstants());
    }

    /**
     * Get all constant value-name pairs as an associative array.
     */
    public static function valueNamePairs(): array
    {
        $constants = self::getConstants();

        return array_combine($constants, array_keys($constants));
    }

    /**
     * Get all constant name-value pairs as an associative array.
     */
    public static function nameValuePairs(): array
    {
        return self::getConstants();
    }

    /**
     * Utility method to get class constants.
     */
    private static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->getConstants();
    }
}
