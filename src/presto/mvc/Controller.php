<?php
namespace Presto\Mvc;

use Presto\Traits\Injectable;

class Controller
{
    use Injectable;

    /** レイアウト */
    public $layout = "";

    /** Viewタイプ */
    public $view_type = View::TYPE_HTML;

    /** テンプレート */
    public $template = "";


    /** コンテンツ */
    public $contents = [];

    /** ぱんくず */
    protected $breadcrumbs = [];


    /**
     * コンテンツを設定
     * @param string $name
     * @param mixed $data
     * @return \Presto\Mvc\Controller
     */
    public function setContent(string $name, $data)
    {
        $this->contents[$name] = $data;
        return $this;
    }


    /**
     * レスポンスの生成
     * @param string $template
     * @param array $contents
     * @return string|mixed
     */
    public function response(string $template=null, array $contents=[])
    {
        // コンテンツをマージ
        $contents = array_merge($this->contents, $contents);

        // ぱんくずを追加
        $contents["breadcrumbs"] = $this->breadcrumbs;

        // Viewテンプレート
        $template = $template ?? $this->template;

        return view()->type($this->view_type)->layout($this->layout)->path($template)->render($contents);
    }
}