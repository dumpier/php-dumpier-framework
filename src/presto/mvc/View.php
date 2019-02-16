<?php
namespace Presto\Mvc;

use Presto\Traits\Singletonable;

// TODO Responseに移動 2019-1-6
class View
{
    use Singletonable;

    const TYPE_HTML = 'html';
    const TYPE_FILE = 'file';
    const TYPE_JSON = 'json';
    const TYPE_JSONP = 'jsonp';

    const TYPE_LIST = [
        self::TYPE_HTML,
        self::TYPE_FILE,
        self::TYPE_JSON,
    ];

    protected $type = self::TYPE_HTML;
    protected $layout = NULL;

    // テンプレートの指定
    public function path(string $path=NULL)
    {
        return $this;
    }


    // viewタイプの指定
    public function type(string $view_type=self::TYPE_HTML)
    {
        $this->type = $view_type;
        return $this;
    }


    public function layout(string $layout)
    {
        $this->layout = $layout;
        return $this;
    }


    /**
     * レンダリング処理
     * @param array $contents
     * @return string|mixed
     */
    public function render(array $contents=[], string $template="", string $layout="")
    {
        timelines("rendering start !");

        switch ($this->type)
        {
            case self::TYPE_JSON:
                return  $this->renderToJson($contents);

            case self::TYPE_FILE:
                return  $this->renderToFile($contents);

            case self::TYPE_HTML:
            default:
                return  $this->renderToHtml($contents, $template, $layout);
        }
    }


    private function renderToJson(array $contents=[])
    {

    }


    private function renderToFile(array $contents=[])
    {

    }


    private function renderToHtml(array $contents=[], string $template="", string $layout="")
    {
        if(!empty($contents["breadcrumbs"]))
        {
            breadcrumb()->adds($contents["breadcrumbs"]);
        }

        $template_file = $this->getDefaultHtmlTemplate($template);

        if( config('cache', 'views.enable') )
        {
            list($phtml, $cache_file) =  $this->loadCache($template_file, $layout);
        }
        else
        {
            $phtml =  $this->loadTemplate($template_file, $layout);
        }

        // コントローラーから渡されたパラメータ
        extract($contents);
        include($cache_file);
    }


    // キャッシュのロード
    private function loadCache(string $template_file, string $layout="")
    {
        $prefix = str_replace("/", ".", str_replace(path("app/views/"), "", trim($template_file,".phtml")));
        $checksum = md5_file($template_file);
        $checksum = 1;

        // キャッシュファイル名
        $cache_file =  path("storages/cache/views/{$prefix}.{$checksum}.phtml");

        if( file_exists($cache_file) && false )
        {
            $phtml =file_get_contents($cache_file);
        }
        else
        {
            // テンプレートをロードする
            $phtml = $this->loadTemplate($template_file, $layout);

            // キャッシュファイルを作成する
            file_put_contents($cache_file, $phtml);
        }

        return [$phtml, $cache_file];
    }

    // テンプレートのロード
    private function loadTemplate(string $template_file, string $layout="")
    {
        timelines("start load template.");

        // テンプレートを読み込む
        $phtml_template = file_get_contents( $template_file );

        // レイアウトを読み込む
        $layout = empty($layout) ? $this->getLayout() : path("app/views/{$layout}.phtml");

        $phtml_layout = file_get_contents( $layout );

        // レイアウトにテンプレートを反映する
        $phtml = preg_replace('/@content/', $phtml_template, $phtml_layout);

        // 独自タグを変換する
        $phtml = template()->convert($phtml);

        timelines("load template completed !");

        return $phtml;
    }


    private function getLayout()
    {
        if($this->layout)
        {
            return path("app/views/{$this->layout}.phtml");
        }

        return path('app/views/html/layouts/html.phtml');
    }


    // テンプレートファイルの取得
    private function getDefaultHtmlTemplate(string $template="")
    {
        if(!empty($template))
        {
            return path("app/views/{$template}.phtml");
        }

        list($controller, $action, $parameters) = routing()->getRouting();
        $template = strtolower($controller) . DIRECTORY_SEPARATOR . strtolower($action);

        $template = str_replace("app\\http\\controllers\\", "", $template);
        $template = str_replace("controller", "", $template);
        $template = stringer()->cleanDirectorySeparator($template);
        $template = path("app/views/html/pages/{$template}.phtml");

        if(! file_exists($template))
        {
            throw new \Exception("htmlテンプレートが見つからない[{$template}]");
        }

        return $template;
    }



}