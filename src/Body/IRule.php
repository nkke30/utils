<?php

namespace Nickimbo\Utils\Validator\Rules;

use stdClass;

interface IRule {
    
    public function run(stdClass|array|string|int $needle): bool;

}

interface IRuleIt {

    public function run(IRule $Rule): bool;

}



?>