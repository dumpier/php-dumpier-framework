<?php
namespace Presto;

use Presto\Traits\Injectable;

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
     * テンプレートの指定
     * @param string $template
     * @return \Presto\Controller
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
     * @return \Presto\Controller
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