<?php
namespace Presto;

use Presto\Traits\Singletonable;
use Presto\Traits\Accessible;

class Routing
{
    use Singletonable;
    use Accessible;

    protected $routings;

    /**
     * ルーティング定義の設定、取得
     * @param array $input
     * @return \Presto\Routing
     */
    public function routings(array $input)
    {
        return $this->accessor("routings", $input);
    }


    /**
     * ルーティングの取得
     * @param string $uri
     * @return string[]|array[]
     */
    public function get(string $uri=null)
    {
        $uri = $uri ?? request()->uri();

        // 未定義の場合、自動で取得
        if(empty($this->routings[$uri]))
        {
            return $this->default();
        }

        $controller = $this->routings[$uri]['controller'];
        $action = empty($this->routings[$uri]['action']) ? 'index' : $this->routings[$uri]['action'];
        // TODO
        $parameter = empty($this->routings[$uri]['parameters']) ? [] : $this->routings[$uri]['parameters'];

        return [$controller, $action, $parameter];
    }


    /**
     * ルーティングの定義がない場合のデフォルトルーティング
     * @example namespace/controller/action/...parameters
     *
     */
    public function default()
    {
        list($controller, $uri, $array) = $this->controller();

        if(empty($controller))
        {
            // コントローラーが見つからなかったらエラー
            throw new \Exception("Page not found[{$uri}]",404);
        }

        // action
        $action = empty($array) ? "index" : array_shift($array);

        // パラメータ
        $parameter = empty($array) ? [] : $array;

        return [$controller, $action, $parameter];
    }


    // コントローラークラスの取得
    private function controller()
    {
        $controller = "";
        $controller_name = "\\App\Http\\Controllers\\";

        $uri = trim(request()->uri(), "/");
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

        return [$controller, $uri, $array];
    }
}