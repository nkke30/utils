<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Exceptions;

class Regex implements IRule {

    private string $Pattern;

    public function __construct(string $Pattern) {
        
        if(@preg_match($Pattern, '') === false) throw new Exceptions\Pattern($Pattern);

        $this->Pattern = $Pattern;
    }

    public function run($needle): bool {

        return @preg_match($this->Pattern, $needle);

    }
}

?>