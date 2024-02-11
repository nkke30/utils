<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\RuleInterface;

use \stdClass;


final class Length implements RuleInterface {

    private int $length;

    public function __construct(int $length) {
        $this->length = $length;
    }

    public function run(stdClass|array|string|int $needle, string $field): bool {

        if(@in_array(@gettype($needle), ['string', 'integer', 'double'])) return @strlen((string)$needle) === $this->length;

        return @sizeof((array)$needle) === $this->length;
    }
}




?>