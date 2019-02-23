<?php
namespace Presto;

use Presto\Traits\Singletonable;
use Presto\Traits\Accessible;

class View
{
    use Singletonable, Accessible;


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

    // レイアウトの指定
    public function layout(string $layout)
    {
        $this->layout = $layout;
        return $this;
        return $this->accessor("layout", $layout);
    }

    // テンプレートの指定
    public function template(string $template)
    {
        $this->template = $template;
        return $this;
        return $this->accessor("template", $template);
    }


    // viewタイプの指定
    public function type(string $view_type=self::HTML)
    {
        $this->type = $view_type;
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
                return  $this->html($contents);
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
            $phtml =  $this->loadCache();
        }
        else
        {
            $phtml =  $this->loadTemplate();
        }

        // コントローラーから渡されたパラメータ
        extract($contents);
        eval("?>" . $phtml);
        // include($cache_file);
    }


    /**
     * キャッシュのロード
     */
    protected function loadCache()
    {
        $template_file = $this->getTemplate();

        $prefix = str_replace("/", ".", str_replace(path("app/views/"), "", trim($template_file,".phtml")));
        $checksum = md5_file($template_file);
        // TODO とりあえずファイル名を固定にする
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
            $phtml = $this->loadTemplate($template_file);

            // キャッシュファイルを作成する
            file_put_contents($cache_file, $phtml);
        }

        return $phtml;
    }

    /**
     * テンプレートのロード
     * @param string $template_file
     * @return string
     */
    protected  function loadTemplate()
    {
        $template_file = $this->getTemplate();

        // テンプレートを読み込む
        $phtml_template = file_get_contents( $template_file );

        // レイアウトを読み込む
        $layout = $this->getLayout();

        $phtml_layout = file_get_contents( $layout );

        // レイアウトにテンプレートを反映する
        $phtml = preg_replace('/@content/', $phtml_template, $phtml_layout);

        // 独自タグを変換する
        $phtml = template()->convert($phtml);

        return $phtml;
    }


    protected function getLayout()
    {
        if($this->layout)
        {
            return $this->getPath($this->layout);
        }

        return framework_path('templates/views/html/layouts/html.phtml');
    }


    protected function getTemplate()
    {
        // 指定した場合
        if($this->template)
        {
            return $this->getPath($this->template);
        }

        // テンプレートが未指定の場合
        list($controller, $action, $parameter) = routing()->get();
        $template = strtolower($controller) . DIRECTORY_SEPARATOR . strtolower($action);

        $template = str_replace("app\\http\\controllers\\", "", $template);
        $template = str_replace("controller", "", $template);
        $template = stringer()->cleanDirectorySeparator($template);

        $template = $this->getPath("html/pages/{$template}");

        if(! file_exists($template))
        {
            throw new \Exception("htmlテンプレートが見つからない[{$template}]");
        }

        return $template;
    }


    /**
     * app/viewsとframework/templates下からViewファイルを探す
     * @param string $template
     * @return string
     */
    private function getPath(string $template)
    {
        $path = path("app/views/{$template}.phtml");

        if(file_exists($path))
        {
            return $path;
        }

        return framework_path("templates/{$template}.phtml");
    }


}