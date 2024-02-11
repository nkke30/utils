<?php

namespace Nickimbo\Utils\Validator\Rules;


class All implements IRule {

    private array $rules;

    public function __construct(IRule ...$Rules) {
        $this->rules = $Rules;
    }

    public function run($needle): bool {
        
        $validCount = 0;

        $needCount = sizeof($this->rules);

        foreach($this->rules as $Rule) {
            if($Rule->run($needle)) $validCount += 1;
        }

        return $validCount === $needCount;
    }
}


?>