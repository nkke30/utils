<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Interfaces\RuleInterface;

class Url implements RuleInterface {

    public function run($needle, string $field): bool {

        return @filter_var($needle, FILTER_VALIDATE_URL);

    }
}


?>