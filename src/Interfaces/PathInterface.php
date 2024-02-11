<?php

namespace Nickimbo\Utils\Interfaces;

use Closure;

interface PathInterface {

    public function findFile(string $fileName, string $dirIncludes): self;

    public function findDir(string $dirName, string $dirIncludes): self;

    public function collect(?string $type): array|string|null;

    public function listFiles(string $dirName, array|callable|closure|null $Extensions): self;

    public function listDirs(string $dirName, callable|Closure|null $Filter): self;

    public function Root(): string;
}

?>