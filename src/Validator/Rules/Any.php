<?php

namespace Nickimbo\Utils\Validator\Rules;

use Nickimbo\Utils\Validator\Interfaces\IRule;

class Any implements IRule {

    private array $rules;

    public function __construct(IRule ...$Rules) {
        $this->rules = $Rules;
    }

    public function run($needle): bool {
        
        $validCount = 0;

        foreach($this->rules as $Rule) {
            if($Rule->run($needle)) $validCount += 1;
        }

        return $validCount > 0;
    }
}




?>