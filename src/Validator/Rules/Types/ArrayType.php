<?php

namespace Nickimbo\Utils\Validator\Rules\Types;


use Nickimbo\Utils\Interfaces\RuleInterface;

class ArrayType implements RuleInterface {

    public function run(mixed $needle, string $field): bool {

        return @is_array($needle);

    }
}

?>