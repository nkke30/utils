<?php
declare(strict_types=1);



namespace Nickimbo\Utils;

use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;

interface IPath {

    public function file(string $fileName, string $dirIncludes): self;

    public function dir(string $dirName, string $dirIncludes): self;

    public function collect(?string $type): ?string;

}


class Path implements IPath {

    private string $rootDir = __DIR__;

    private ?string $resultDir;

    private ?string $resultFile;


    private function contains(string $haystack, string $needle): bool {

        return str_contains(strtolower($haystack), strtolower($needle));

    }
    public function construct(?string $rootDir = null): void {
        if ($rootDir !== null) $this->rootDir = $rootDir;
    }

    public function file(string $name, string $dirIncludes = ''): self {
        $foundFile = null;

        $localDir = '/';

        $dirs = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $this->rootDir)));
    
        foreach ($dirs as $dir) {

            $currentDir = $localDir . $dir . DIRECTORY_SEPARATOR;

            $scanDir = glob($currentDir . '*');

            if(in_array($currentDir . $name, $scanDir) && $this->contains($currentDir.$name, $dirIncludes)) {
                $foundFile = $currentDir.$name;
                break;
            }
            else {
                $DirIt = new RecursiveDirectoryIterator($currentDir, FilesystemIterator::SKIP_DOTS|RecursiveIteratorIterator::SELF_FIRST);


                $Iterator = new RecursiveIteratorIterator($DirIt);

                foreach($Iterator as $currentFile) {
                    if($currentFile->isFile() && $name == $currentFile->getFilename() && $this->contains($currentFile->getPathname(), $dirIncludes)) {
                        $foundFile = $currentFile->getPathname();
                        break 2;
                    } 
                }

            } 

            
            $localDir .= ($dir . DIRECTORY_SEPARATOR);
        }
    

        $this->resultFile = $foundFile;

        return $this;
    }

    public function dir(string $dirName, string $dirContains = ''): self {

        $localDir = '/';
        $foundDir = null;

        $dirs = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $this->rootDir)));



        $formattedDir = ltrim(rtrim($dirName, '/'), '/') . '/';
        

        foreach ($dirs as $dir) {

            $currentDir = $localDir . $dir . DIRECTORY_SEPARATOR;


            $scanDir = glob($currentDir . '*', GLOB_ONLYDIR);

            //echo $currentDir.$formattedDir.PHP_EOL;

            if((is_dir($currentDir.$formattedDir) || in_array($currentDir.$formattedDir, $scanDir)) && $this->contains($currentDir.$formattedDir, $dirContains)) {
                $foundDir = $currentDir.$dirName;
                break;
            } elseif($currentDir == '/'.$formattedDir && $this->contains($currentDir, $dirContains)) {
                $foundDir = $currentDir;
                break;
            } else {
                $Dirs = new RecursiveDirectoryIterator($currentDir, FilesystemIterator::SKIP_DOTS);
                $Iterator = new RecursiveIteratorIterator($Dirs);
                foreach($Iterator as $Entry) {
                    if($Entry->isDir() && $this->contains($Entry->getPath(), $dirContains) && str_ends_with($Entry->getPath(), rtrim($formattedDir, '/'))) {
                        $foundDir = $Entry->getPath();
                        break 2;
                    }
                }
            }
            $localDir .= ($dir . DIRECTORY_SEPARATOR);
        }

        $this->resultDir = $foundDir;

        return $this;
    }

    public function collect(?string $type = null): ?string {
        switch($type) {
            case 'dir':
                return $this->resultDir;
            case 'file':
                return $this->resultFile;
            default:
                return $this->resultFile;
        }
    }

    public function match(string $haystack, string $needle): string {
        $Pos = strpos($haystack, $needle);

        if ($Pos !== false) return substr($haystack, 0, $Pos + strlen($needle));
        else return $haystack;
    }

}
?>