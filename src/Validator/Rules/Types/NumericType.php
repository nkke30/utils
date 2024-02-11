<?php

namespace Nickimbo\Utils\Validator\Rules\Types;


use Nickimbo\Utils\Interfaces\RuleInterface;

class NumericType implements RuleInterface {

    public function run(mixed $needle, string $field): bool {

        return @is_numeric($needle) || @is_int($needle) || @in_array(@gettype($needle), ['integer', 'double']);

    }
}

?>