<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.04.2016
 * Time: 17:24.
 */

namespace Turted\TurtedBundle\Service;

class FileGetContentsWrapper
{
    public function fileGetContents($server, $use_include_path, $context)
    {
        return @file_get_contents($server, $use_include_path, $context);
    }
}
