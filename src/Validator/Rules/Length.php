<?php

namespace Nickimbo\Utils\Validator\Rules;


use stdClass;

class Length implements IRule {

    private int $length;

    public function __construct(int $length) {
        $this->length = $length;
    }

    public function run(stdClass|array|string|int $needle): bool {


        if(@in_array(@gettype($needle), ['string', 'int'])) return @strlen((string)$needle) === $this->length;

        return @sizeof((array)$needle) === $this->length;
    }
}

?>