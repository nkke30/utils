<?php

namespace Nickimbo\Utils\Validator\Rules;


class ArrayType implements Rule {

    public function run(mixed $needle): bool {

        return @is_array($needle);

    }
}


class StringType implements Rule {

    public function run(mixed $needle): bool {

        return $needle !== null && @is_string($needle);

    }
}

class BooleanType implements Rule {
    
    public function run(mixed $needle): bool {
        return @is_bool($needle);
    }

}

?>