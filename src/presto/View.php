<?php
namespace Presto;

use Presto\Traits\Singletonable;

// TODO Responseに移動 2019-1-6
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

    // テンプレートの指定
    public function template(string $template)
    {
        $this->template = $template;
        return $this;
    }


    // viewタイプの指定
    public function type(string $view_type=self::HTML)
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
    public function render(array $contents=[])
    {
        timelines("rendering start !");

        switch ($this->type)
        {
            case self::JSON:
                return  $this->json($contents);

            case self::FILE:
                return  $this->file($contents);

            case self::HTML:
            default:
                return  $this->html($contents, $template, $layout);
        }
    }


    protected function json(array $contents=[])
    {

    }


    protected function file(array $contents=[])
    {

    }


    protected function html(array $contents=[])
    {
        if(!empty($contents["breadcrumbs"]))
        {
            breadcrumb()->adds($contents["breadcrumbs"]);
        }

        if( config('cache', 'views.enable') )
        {
            list($phtml, $cache_file) =  $this->loadCache();
        }
        else
        {
            $phtml =  $this->loadTemplate();
        }

        // コントローラーから渡されたパラメータ
        extract($contents);
        include($cache_file);
    }


    // キャッシュのロード
    private function loadCache()
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
    private function loadTemplate()
    {
        // テンプレートを読み込む
        $phtml_template = file_get_contents( $template_file );

        // レイアウトを読み込む
        $layout = empty($layout) ? $this->getLayout() : $this->framework($layout);

        $phtml_layout = file_get_contents( $layout );

        // レイアウトにテンプレートを反映する
        $phtml = preg_replace('/@content/', $phtml_template, $phtml_layout);

        // 独自タグを変換する
        $phtml = template()->convert($phtml);

        return $phtml;
    }


    private function getLayout()
    {
        if($this->layout)
        {
            return $this->framework("app/views/{$this->layout}.phtml");
        }

        return path('app/views/html/layouts/html.phtml');
    }


    // テンプレートファイルの取得
    private function getDefaultHtmlTemplate(string $template="")
    {
        if(!empty($template))
        {
            return $this->framework($template);
        }

        // テンプレートが未指定の場合
        list($controller, $action, $parameter) = routing()->get();
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


    private function framework(string $template)
    {
        $path = framework_path("templates/{$template}.phtml");
        if(file_exists($path))
        {
            return $path;
        }

        return path("app/views/{$template}.phtml");
    }

}