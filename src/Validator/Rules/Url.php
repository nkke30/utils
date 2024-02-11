<?php

namespace Nickimbo\Utils\Validator\Rules;

class Url implements Rule {

    public function run($needle): bool {

        return @filter_var($needle, FILTER_VALIDATE_URL);

    }
}

?>