<?php

namespace Nickimbo\Utils\Validator\Rules;


use Nickimbo\Utils\Validator\Exceptions;
use stdClass;

class ArrayType implements IRule {

    public function run(mixed $needle): bool {

        return @is_array($needle);

    }
}


class StringType implements IRule {

    public function run(mixed $needle): bool {

        return $needle !== null && @is_string($needle);

    }
}

class BooleanType implements IRule {
    
    public function run(mixed $needle): bool {
        return @is_bool($needle);
    }

}

class All implements IRule {

    private array $rules;

    public function __construct(IRule ...$Rules) {
        $this->rules = $Rules;
    }

    public function run($needle): bool {
        
        $validCount = 0;

        $needCount = sizeof($this->rules);

        foreach($this->rules as $Rule) {
            if($Rule->run($needle)) $validCount += 1;
        }

        return $validCount === $needCount;
    }
}


class Any implements IRule {

    private array $rules;

    public function __construct(IRule ...$Rules) {
        $this->rules = $Rules;
    }

    public function run($needle): bool {
        
        $validCount = 0;

        foreach($this->rules as $Rule) {
            if($Rule->run($needle)) $validCount += 1;
        }

        return $validCount > 0;
    }
}

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

class Range implements IRule {

    private array $Range;

    public function __construct(array $range) {
        if(@sizeof($range) !== 2) throw new Exceptions\Range($range);

        array_map(function($val) use($range) {
            if(@is_int($val) === false) throw new Exceptions\Range($range);
        }, $range);

        $this->Range = $range;
    }

    public function run(stdClass|array|string|int $needle): bool {

        $Length = @in_array(@gettype($needle), ['string', 'int']) ? @strlen((string) $needle) : @sizeof((array) $needle);

        return $Length >= $this->Range[0] && $Length <= $this->Range[1];
    }
}

class Regex implements IRule {

    private string $Pattern;

    public function __construct(string $Pattern) {
        
        if(@preg_match($Pattern, '') === false) throw new Exceptions\Pattern($Pattern);

        $this->Pattern = $Pattern;
    }

    public function run($needle): bool {

        return @preg_match($this->Pattern, $needle);

    }
}

class Url implements IRule {

    public function run($needle): bool {

        return @filter_var($needle, FILTER_VALIDATE_URL);

    }
}

?>