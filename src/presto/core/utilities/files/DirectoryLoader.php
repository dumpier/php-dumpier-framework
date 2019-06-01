<?php
namespace Presto\Core\Utilities\Files;

use Presto\Core\Traits\Singletonable;

class DirectoryLoader
{
    use Singletonable;

    /**
     * ツリー形式で取得 TODO
     * @param string $path
     * @param string $chain
     * @return array
     */
    public function tree(string $path, string $chain="")
    {
        if(! $this->isDirectory($path))
        {
            return [];
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


    /**
     * フォルダとファイル一覧の取得
     * @param string $base
     * @param string $path
     * @return array
     */
    public function list(string $basedir, string $path="")
    {
        $fullpath = "{$basedir}{$path}";
        $path = empty($path) ? $basedir : $path;

        $this->checkIsDirectory($fullpath);

        $directories = [];
        $files = [];

        foreach (glob("{$fullpath}/*") as $sub_path)
        {
            if($this->isDirectory($sub_path))
            {
                $directories[] = str_replace($basedir, "", $sub_path);
                continue;
            }

            if($this->isFile($sub_path))
            {
                $size = util()->file()->byte($sub_path);
                $name = str_replace($basedir, "", $sub_path);

                $files[] = ["size"=>$size, "name"=>$name];
            }
        }

        return [$directories, $files];
    }


    /**
     * ファイル一覧
     * @param string $basedir
     * @param bool $require_file_size ファイルサイズを取得するか
     * @return array
     */
    public function files(string $basedir, bool $require_file_size=false)
    {
        $files = [];

        foreach (glob("{$basedir}/*") as $file)
        {
            if($this->isDirectory($file))
            {
                continue;
            }

            if($require_file_size)
            {
                $size = util()->file()->byte($file);
                $files[] = ["size"=>$size, "name"=>$file];
            }
            else
            {
                $files[] = $file;
            }
        }

        return $files;
    }


    public function directories(string $basedir)
    {
        $directories = [];

        foreach (glob("{$basedir}/*") as $sub)
        {
            if($this->isDirectory($sub))
            {
                $directories[] = $sub;
            }
        }

        return $directories;
    }



    /**
     * フォルダであるか
     * @param string $path
     * @return boolean
     */
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


    /**
     * ファイルであるか
     * @param string $path
     * @return boolean
     */
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

        throw new \Exception("Is not directory !\n{$path}");
    }

    public function checkIsFile(string $path)
    {
        if($this->isFile($path))
        {
            return true;
        }

        throw new \Exception("Is not file !\n{$path}");
    }
}