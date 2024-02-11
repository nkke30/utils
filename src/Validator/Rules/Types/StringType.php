<?php

namespace Nickimbo\Utils\Validator\Rules\Types;


use Nickimbo\Utils\Validator\Interfaces\RuleInterface;

class StringType implements RuleInterface {

public function run(mixed $needle, string $field): bool {

    return $needle !== null && @is_string($needle);

}
}