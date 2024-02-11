<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\RuleInterface;
use Nickimbo\Utils\Validator\Exceptions;



class Regex implements RuleInterface {

    private string $Pattern;

    public function __construct(string $Pattern) {
        
        if(@preg_match($Pattern, '') === false) throw new Exceptions\InvalidPattern($Pattern);

        $this->Pattern = $Pattern;
    }

    public function run($needle, $field): bool {

        return @preg_match($this->Pattern, $needle);

    }
}


?>