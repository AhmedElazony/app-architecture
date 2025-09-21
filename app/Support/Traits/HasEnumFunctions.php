<?php

namespace App\Support\Traits;

trait HasEnumFunctions
{
    /**
     * Get all enum values.
     *
     * @return array<int|string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_map(fn ($case) => $case->name, self::cases());
    }

    /**
     * Get enum options with name, value, label.
     *
     * @return array<array{name: string, value: int|string, label: string}>
     */
    public static function options(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }, self::cases());
    }

    /**
     * Get enum labels as [value => label].
     *
     * @return array<string|int, string>
     */
    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }

    /**
     * Get select-ready array like [value => label].
     *
     * @return array<string|int, string>
     */
    public static function toSelectArray(): array
    {
        return self::labels();
    }

    /**
     * Get a map of enum names to values.
     *
     * @return array<string, string|int>
     */
    public static function toKeyedArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Get a map of values to translated labels.
     *
     * @return array<string|int, string>
     */
    public static function toKeyedLabelsArray(): array
    {
        return self::labels();
    }

    /**
     * Get enum instance from value.
     */
    public static function fromValue(string|int $value): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get the translated label for the enum case.
     */
    public function label(): string
    {
        return __("enums.{$this->value}");
    }
}
