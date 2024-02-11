<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\RuleInterface;
use Nickimbo\Utils\Validator\Exceptions;


use \stdClass;


class NumericRange implements RuleInterface {

    private array $Range;

    public function __construct(array $range) {
        if(@sizeof($range) !== 2) throw new Exceptions\InvalidRange($range);

        array_map(function($val) use($range) {
            if(@is_int($val) === false) throw new Exceptions\InvalidRange($range);
        }, $range);

        $this->Range = $range;
    }

    public function run(stdClass|array|string|int $needle, string $field): bool {

        if(@in_array(@gettype($needle), ['string', 'int'])) return $needle >= $this->Range[0] && $needle <= $this->Range[1];

        else {
            return @array_reduce((array) $needle, function($Carry, $Item) {
                return @in_array(@gettype($Item), ['integer', 'double']) && ($Item >= $this->Range[0] && $Item <= $this->Range[1]);
            }, 0);
        }
    }
}



?>