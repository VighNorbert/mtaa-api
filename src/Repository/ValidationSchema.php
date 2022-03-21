<?php

namespace App\Repository;

class ValidationSchema
{

    const VALIDATE_STRING = 0;
    const VALIDATE_NUMBER = 1;
    const VALIDATE_NUMBER_GTZ = 2;
    const VALIDATE_EMAIL = 3;
    const VALIDATE_PHONE = 4;

    private array|int|null $allowed_values;
    private string|int|null $default_value;
    private ?int $min;
    private ?int $max;

    /**
     * @param array|int|null $allowed_values
     *      array of allowed_values or a string specifying a set of allowed values (see constants in this file)
     * @param string|int|null $default_value
     *      string or int value to be used if validation fails
     *      null if you want to throw an exception
     * @param int|null $min
     *      - input is string: minimal length of input
     *      - input is int: minimal value of input
     *      null to disable
     * @param int|null $max
     *      - input is string: maximal length of input
     *      - input is int: maximal value of input
     *      null to disable
     */
    public function __construct(array|int|null $allowed_values = null, string|int|null $default_value = null, int|null $min = null, int|null $max = null)
    {
        $this->allowed_values = $allowed_values;
        $this->default_value = $default_value;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return array|int|null
     */
    public function getAllowedValues(): array|int|null
    {
        return $this->allowed_values;
    }

    /**
     * @return int|string|null
     */
    public function getDefaultValue(): string|int|null
    {
        return $this->default_value;
    }

    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

}