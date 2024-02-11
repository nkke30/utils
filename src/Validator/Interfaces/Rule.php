<?php

namespace Nickimbo\Utils\Validator\Interfaces;

use stdClass;

interface IRule {
    
    public function run(stdClass|array|string|int $needle): bool;

}



?>