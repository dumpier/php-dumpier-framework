<?php
namespace Presto;

use Presto\Core\Traits\Injectable;
use Presto\Core\Views\View;

class Controller
{
    use Injectable;

    /** レイアウト */
    public $layout = "";

    /** Viewタイプ */
    public $view_type = View::HTML;

    /** テンプレート */
    public $template = "";


    /** コンテンツ */
    public $contents = [];

    /** ぱんくず */
    protected static $breadcrumb = [];

    /**
     * ぱんくずの追加
     * @param string $name
     * @param string $url
     */
    public function breadcrumb(string $name, string $url="")
    {
        static::$breadcrumb[] = ["name"=>$name, "url"=>$url];
    }

    /**
     * テンプレートの指定
     * @param string $template
     * @return \Presto\Core\Controller
     */
    public function template(string $template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * コンテンツを設定
     * @param string $name
     * @param mixed $data
     * @return \Presto\Core\Controller
     */
    public function content(string $name, $data)
    {
        $this->contents[$name] = $data;
        return $this;
    }


    /**
     * レスポンスの生成
     * @param array $contents
     * @return string|mixed
     */
    public function response(array $contents=[])
    {
        // コンテンツをマージ
        $contents = array_merge($this->contents, $contents);

        // ぱんくずを追加
        $contents["breadcrumb"] = static::$breadcrumb;

        return view()->type($this->view_type)->layout($this->layout)->template($this->template)->render($contents);
    }
}