<?php
declare(strict_types=1);

namespace Nickimbo\Utils\Interfaces;



interface IPath {

    public function file(string $fileName, string $targetDir): self;

    public function dir(string $dirName, string $targetDir): self;

    public function collect(?string $type): ?string;

}

?>