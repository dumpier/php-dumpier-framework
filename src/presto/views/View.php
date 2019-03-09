<?php
namespace Presto\Views;

use Presto\Traits\Singletonable;
use Presto\Views\Renders\HtmlRender;

class View
{
    use Singletonable;

    const HTML = "html";
    const STREAM = "stream";
    const FILE = "file";
    const JSON = "json";
    const JSONP = "jsonp";

    const LIST = [
        self::HTML,
        self::STREAM,
        self::FILE,
        self::JSON,
        self::JSONP,
    ];

    protected $type = self::HTML;
    protected $layout = NULL;
    protected $template = "";


    /**
     * viewタイプの指定
     * @param string $view_type
     * @return \Presto\Views\View
     */
    public function type(string $view_type=self::HTML)
    {
        $this->type = $view_type;
        return $this;
    }

    /**
     * レイアウトの指定
     */
    public function layout(string $layout)
    {
        $this->layout = $layout;
        $this->type = self::HTML;
        return $this;
    }

    /**
     * テンプレートの指定
     * @param string $template
     * @return \Presto\Views\View
     */
    public function template(string $template)
    {
        $this->template = $template;
        return $this;
    }


    /**
     * レンダリング処理
     * @param array $contents
     * @return string|mixed
     */
    public function render(array $contents=[])
    {
        timelines("rendering start !");

        switch ($this->type)
        {
            case self::JSON:
                return  $this->json($contents);

            case self::JSONP:
                return  $this->jsonp($contents);

            case self::STREAM:
                return  $this->stream($contents);

            case self::FILE:
                return  $this->file($contents);

            case self::HTML:
            default:
                return  HtmlRender::instance()->html($contents);
        }
    }


    protected function json(array $contents=[])
    {

    }

    protected function jsonp(array $contents=[])
    {

    }

    protected function file(array $contents=[])
    {

    }

    protected function stream(array $contents=[])
    {

    }


    // ------------------------------------------------------------------------
    // HTML
    // ------------------------------------------------------------------------
    /**
     * レイアウトの取得
     * @return string
     */
    public function getHtmlLayout()
    {
        if($this->layout)
        {
            return $this->getHtmlPath($this->layout);
        }

        return framework_path('templates/html/layouts/html.phtml');
    }


    /**
     * テンプレートの取得
     * @throws \Exception
     * @return string
     */
    public function getHtmlTemplate()
    {
        // 指定した場合
        if($this->template)
        {
            return $this->getHtmlPath($this->template);
        }

        // テンプレートが未指定の場合
        list($controller, $action, $parameter) = routing()->get();
        $template = strtolower($controller) . DIRECTORY_SEPARATOR . strtolower($action);

        $template = str_replace("app\\http\\controllers\\", "", $template);
        $template = str_replace("controller", "", $template);
        $template = stringer()->cleanDirectorySeparator($template);

        $template = $this->getHtmlPath("html/pages/{$template}");

        if(! file_exists($template))
        {
            throw new \Exception("htmlテンプレートが見つからない[{$template}]");
        }

        return $template;
    }


    /**
     * app/templatesとframework/templates下からViewファイルを探す
     * @param string $template
     * @return string
     */
    private function getHtmlPath(string $template)
    {
        if(file_exists($path = template_path("{$template}.phtml")))
        {
            return $path;
        }

        return framework_path("templates/{$template}.phtml");
    }
    // ------------------------------------------------------------------------

}