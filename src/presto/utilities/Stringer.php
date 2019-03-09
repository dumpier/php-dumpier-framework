<?php
namespace Presto\Utilities;


use Presto\Traits\Singletonable;

class Stringer
{
    use Singletonable;

    /**
     * パスカルケースに変換
     * @param string $string
     * @return mixed
     */
    public function toPascal(string $string)
    {
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        $string = str_replace('\\', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '\\', $string);

        return $string;
    }


    /**
     * スネークケースに変換
     * @param string $string
     * @return string
     */
    public function toSnake(string $string)
    {
        return strtolower(preg_replace('/[a-z]+(?=[A-Z])|[A-Z]+(?=[A-Z][a-z])/', '\0_', $string));
    }


    /**
     * キャメルケースに変換
     * @param string $string
     * @return string
     */
    public function toCamel(string $string)
    {
        $string = $this->toPascal($string);
        $string[0] = strtolower($string[0]);
        return $string;
    }


    /**
     * フォルダ区切りコードの整理
     * @param string $path
     * @return string
     */
    public function cleanDirectorySeparator(string $path)
    {
        return (string)preg_replace("/(\\\|\/){1,}/", DIRECTORY_SEPARATOR, $path);
    }


    /**
     * Jsonフォーマットが正しいか確認する
     * @param string $string
     * @return boolean
     */
    public function isJson(string $string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}