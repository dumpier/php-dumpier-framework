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

    public function __construct()
    {
        // ぱんくずの継承
        if( get_parent_class() && property_exists(parent, "breadcrumb") && !empty(parent::$breadcrumb))
        {
            self::$breadcrumb = property_exists($this, "breadcrumb") ? array_merge(parent::$breadcrumb, self::$breadcrumb) : parent::$breadcrumb;
        }
    }

    /**
     * コンテンツを設定
     * @param string $name
     * @param mixed $data
     * @return \Presto\Controller
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
        $contents["breadcrumb"] = static::$breadcrumb;

        // Viewテンプレート
        $template = $template ?? $this->template;

        return view()->type($this->view_type)->layout($this->layout)->template($template)->render($contents);
    }
}