<?php

namespace Nickimbo\Utils\Validator\Rules;


use Nickimbo\Utils\Interfaces\RuleInterface;
use stdClass;

class Literal implements RuleInterface {

    private string|int|array|stdClass $literal;

    public function __construct(string|int|array|stdClass $literal) {
        $this->literal = $literal;
    }

    public function run(mixed $needle, string $field): bool {

        if ($this->literal instanceof stdClass or @is_array($this->literal)) {
            return $needle instanceof stdClass or @is_array($needle) ? $this->literal === $needle : false;
        } else {
            return $needle === $this->literal;
        }
    }
}

?>