<?php

namespace Nickimbo\Utils\Validator\Rules;

use stdClass;

interface Rule {
    
    public function run(stdClass|array|string|int $needle): bool;

}

interface RuleIt {

    public function run(Rule $Rule): bool;

}



?>