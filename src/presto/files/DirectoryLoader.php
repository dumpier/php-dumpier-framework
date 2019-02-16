<?php
namespace Presto\Files;

use Presto\Traits\Singletonable;
use Presto\CatchaleException;

class DirectoryLoader
{
    use Singletonable;

    // ツリー形式で取得
    public function tree(string $path, string $chain="")
    {
        if(! $this->isDirectory($path))
        {
            return null;
        }

        $chain = empty($chain) ? end(explode()) : $chain;

        $tree = [];

        foreach (glob("{$path}/*") as $key=>$sub_path)
        {
            if($this->isDirectory($sub_path))
            {
                var_dump(dirname($sub_path));

                $sub_tree = $this->tree($sub_path, $recursion+1);
                if(!empty($sub_tree))
                {
                    $tree = array_merge($tree, $sub_tree);
                }
            }
            else
            {
                $tree[$recursion][$key] = $sub_path;
            }
        }

        return $tree;
    }

    // フォルダとファイル一覧の取得
    public function list(string $path)
    {
        $this->checkIsDirectory($path);

        $directories = [];
        $files = [];

        foreach (glob("{$path}/*") as $sub_path)
        {
            if($this->isDirectory($sub_path))
            {
                $directories[] = $sub_path;
                continue;
            }

            if($this->isFile($sub_path))
            {
                $files[] = $sub_path;
                continue;
            }
        }

        return [$directories, $files];
    }

    public function isDirectory(string $path)
    {
        if(! file_exists($path))
        {
            return false;
        }

        if(is_dir($path))
        {
            return true;
        }

        return false;
    }

    public function isFile(string $path)
    {
        if(! file_exists($path))
        {
            return false;
        }

        if(is_file($path))
        {
            return true;
        }

        return false;
    }

    public function checkIsDirectory(string $path)
    {
        if($this->isDirectory($path))
        {
            return true;
        }

        throw new CatchaleException("Is not directory !\n{$path}");
    }

    public function checkIsFile(string $path)
    {
        if($this->isFile($path))
        {
            return true;
        }

        throw new CatchaleException("Is not file !\n{$root_path}");
    }
}