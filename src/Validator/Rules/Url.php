<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\RuleInterface;

class Url implements RuleInterface {

    public function run($needle, string $field): bool {

        return @filter_var($needle, FILTER_VALIDATE_URL);

    }
}


?>