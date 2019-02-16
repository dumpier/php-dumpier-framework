<?php
namespace Presto;

use Presto\Traits\Singletonable;

class Routing
{
    use Singletonable;

    public $routings;

    public function getRouting(string $uri=null)
    {
        $uri = $uri ?? $this->getUri();

        if(empty($this->routings[$uri]))
        {
            return $this->default();
        }

        $controller = $this->routings[$uri]['controller'];
        $action = empty($this->routings[$uri]['action']) ? 'index' : $this->routings[$uri]['action'];
        // TODO
        $parameters = empty($this->routings[$uri]['parameters']) ? [] : $this->routings[$uri]['parameters'];

        return [$controller, $action, $parameters];
    }


    public function getUri()
    {
        // ベースURI
        $uri = preg_replace("/^" . preg_quote(baseurl()) . "/", "", $_SERVER['REQUEST_URI']);
        $uri = preg_replace("/\?.*/", "", $uri);
        return $uri;
    }



    /**
     * @example namespace/controller/action/...parameters
     *
     */
    public function default()
    {
        $controller_name = "\\App\Http\\Controllers\\";
        $controller = "";
        $action = "";
        $parameters = [];

        $uri = trim($this->getUri(), "/");
        $array = explode("/", $uri);

        foreach ($array as $string)
        {
            array_shift($array);
            $controller_name .= stringer()->toPascal($string);

            if( class_exists($controller_name . "Controller") )
            {
                $controller = $controller_name . "Controller";
                break;
            }

            $controller_name .= "\\";
        }

        if(empty($controller))
        {
            // コントローラーが見つからなかったらエラー
            throw new \Exception("Page not found[{$uri}]",404);
        }

        // action
        $action = empty($array) ? "index" : array_shift($array);

        // パラメータ
        $parameters = empty($array) ? [] : $array;

        return [$controller, $action, $parameters];
    }
}