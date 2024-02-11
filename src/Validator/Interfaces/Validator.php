<?php

namespace Nickimbo\Utils\Validator\Interfaces;

interface IValidator {


    public function setFields(string ...$Fields): self;

    public function setRules(\Nickimbo\Utils\Validator\Interfaces\IRule ...$Rules): self;

    public function run(): void;

    public function errors(): ?array;

    public function isValid(): bool;
}



?>