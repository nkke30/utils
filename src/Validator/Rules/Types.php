<?php

namespace Nickimbo\Utils\Validator\Rules;


use Nickimbo\Utils\Validator\Interfaces\IRule;

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

?>