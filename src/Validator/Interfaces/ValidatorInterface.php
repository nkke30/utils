<?php

namespace Nickimbo\Utils\Validator\Interfaces;


interface ValidatorInterface {


    public function setFields(string ...$Fields): self;

    public function setRules(RuleInterface ...$Rules): self;

    public function run(): void;

    public function errors(): ?array;

    public function isValid(): bool;
}

?>