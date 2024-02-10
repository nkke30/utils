<?php
declare(strict_types=1);



namespace Nickimbo\Utils;

use Closure;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

interface IPath {

    public function findFile(string $fileName, string $dirIncludes): Path;

    public function findDir(string $dirName, string $dirIncludes): Path;

    public function collect(?string $type): array|string|null;

    public function listFiles(string $dirName, array|callable|closure|null $Extensions): Path;

    public function listDirs(string $dirName, callable|Closure|null $Filter): Path;

    public function Root(): string;
}


class Path implements IPath {

    private string $rootDir = __DIR__;

    private ?string $Dir;

    private ?string $File;
    
    private ?array $Files;

    private ?array $Dirs;


    private function contains(string $haystack, string $needle): bool {

        return str_contains(strtolower($haystack), strtolower($needle));

    }
    public function construct(?string $rootDir = null): void {
        if ($rootDir !== null) $this->rootDir = $rootDir;
    }

    public function findFile(string $name, string $dirIncludes = ''): Path {
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
                foreach(array_filter($scanDir, 'is_dir') as $xdDir) {
                $DirIt = new RecursiveDirectoryIterator($xdDir, FilesystemIterator::SKIP_DOTS);


                $Iterator = new RecursiveIteratorIterator($DirIt, RecursiveIteratorIterator::SELF_FIRST|RecursiveIteratorIterator::CATCH_GET_CHILD);

                foreach($Iterator as $currentFile) {
                    if($currentFile->isFile() && $name == $currentFile->getFilename() && $this->contains($currentFile->getPathname(), $dirIncludes)) {
                        $foundFile = $currentFile->getPathname();
                        break 2;
                    } 
                }
            }
            } 

            
            $localDir .= ($dir . DIRECTORY_SEPARATOR);
        }

        $this->File = $foundFile;

        return $this;
    }

    public function findDir(string $dirName, string $dirContains = ''): Path {

        $localDir = '/';
        $foundDir = null;

        $dirs = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $this->rootDir)));



        $formattedDir = ltrim(rtrim($dirName, '/'), '/') . '/';
        

        foreach ($dirs as $dir) {

            $currentDir = $localDir . $dir . DIRECTORY_SEPARATOR;


            $scanDir = glob($currentDir . '*', GLOB_ONLYDIR);

            if((is_dir($currentDir.$formattedDir) || in_array($currentDir.$formattedDir, $scanDir)) && $this->contains($currentDir.$formattedDir, $dirContains)) {
                $foundDir = $currentDir.$dirName;
                break;
            } elseif($currentDir == '/'.$formattedDir && $this->contains($currentDir, $dirContains)) {
                $foundDir = $currentDir;
                break;
            } else {
                $Dirs = new RecursiveDirectoryIterator($currentDir, FilesystemIterator::SKIP_DOTS);
                $Iterator = new RecursiveIteratorIterator($Dirs, RecursiveIteratorIterator::CATCH_GET_CHILD);
                foreach($Iterator as $Entry) {
                    if($Entry->isDir() && $this->contains($Entry->getPath(), $dirContains) && str_ends_with($Entry->getPath(), rtrim($formattedDir, '/'))) {
                        $foundDir = $Entry->getPath();
                        break 2;
                    }
                }
            }
            $localDir .= ($dir . DIRECTORY_SEPARATOR);
        }

        $this->Dir = $foundDir;

        return $this;
    }

    public function collect(?string $type = null): array|string|null {
        switch($type) {
            case 'dir':
                return $this->Dir;
            case 'file':
                return $this->File;
            case 'files': 
                return $this->Files;
            case 'dirs':
                return $this->Dirs;
            default:
                return $this->File;
        }
    }

    public function listFiles(string $dirName, callable|Closure|null|array $Filter): Path {
        if (!is_dir($dirName)) return $this;

        $Files = [];

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirName, FilesystemIterator::UNIX_PATHS));

        foreach ($it as $File) {
            if ($File->isDir()) continue;
            if ($Filter !== null) {
                if(is_array($Filter)) foreach ($Filter as $Ext): if(str_ends_with($File->getFilename(), $Ext)) $Files[] = $File->getPathname(); endforeach;
                else {
                    if ($Filter($File)) $Files[] = $File->getPathname();
                }
            } else {
                $Files[] = $File->getPathname();
            }
        }

        $this->Files = $Files;



        return $this;

    }

    public function listDirs(string $dirName, callable|Closure|null $Filter): Path {
        if (!is_dir($dirName)) return $this;

        $Dirs = [];

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirName, FilesystemIterator::UNIX_PATHS));
    
        foreach ($it as $File) {
            if ($File->isDir()) {
                if ($Filter !== null) {
                    if ($Filter($File)) $Dirs[] = $File->getPath() . DIRECTORY_SEPARATOR;
                } else {
                    $Dirs[] = $File->getPath() . DIRECTORY_SEPARATOR;
                }
            }
        }

        $this->Dirs = $Dirs;

        return $this;

    }

    public function Root(): string {
       return '/' . array_values(array_filter(explode('/', $this->rootDir)))[0]; 
    }
}
?>