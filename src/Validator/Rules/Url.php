<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\IRule;



class Url implements IRule {

    public function run($needle): bool {

        return @filter_var($needle, FILTER_VALIDATE_URL);

    }
}


?>