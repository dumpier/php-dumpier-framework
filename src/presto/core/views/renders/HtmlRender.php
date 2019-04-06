<?php
namespace Presto\Core\Views\Renders;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Utilities\Breadcrumb;
use Presto\Core\Views\View;
use Presto\Core\Views\TemplateEngine;
use Presto\Core\Utilities\Files\ConfigLoader;
use Presto\Core\Utilities\Pather;

class HtmlRender
{
    use Singletonable;

    public function html(array $contents=[])
    {
        if(!empty($contents["breadcrumb"]))
        {
            Breadcrumb::instance()->adds($contents["breadcrumb"]);
        }

        if( ConfigLoader::instance()->get('cache', 'templates.enable') )
        {
            // テンプレートキャッシュのロード
            $phtml =  $this->loadCache();
        }
        else
        {
            // テンプレートのロード
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
        $template_file = View::instance()->getHtmlTemplate();

        $prefix = str_replace("/", ".", str_replace(Pather::instance()->template(), "", trim($template_file,".phtml")));
        $checksum = md5_file($template_file);
        // TODO とりあえずファイル名を固定にする
        $checksum = 1;

        // キャッシュファイル名
        $cache_file =  Pather::instance()->cache_template("{$prefix}.{$checksum}.phtml");

        if( file_exists($cache_file) )
        {
            // キャッシュファイルがある場合
            $phtml =file_get_contents($cache_file);
        }
        else
        {
            // テンプレートをロードする
            $phtml = $this->loadTemplate();

            // キャッシュファイルを作成する
            file_put_contents($cache_file, $phtml);
        }

        return $phtml;
    }

    /**
     * テンプレートのロード
     * @return string
     */
    protected  function loadTemplate()
    {
        $template_file = View::instance()->getHtmlTemplate();

        // テンプレートを読み込む
        $phtml_template = file_get_contents( $template_file );

        // レイアウトを読み込む
        $layout = View::instance()->getHtmlLayout();

        $phtml_layout = file_get_contents( $layout );

        // レイアウトにテンプレートを反映する
        $phtml = preg_replace('/@content/', $phtml_template, $phtml_layout);

        // 独自タグを変換する
        $phtml = TemplateEngine::instance()->convert($phtml);

        return $phtml;
    }

}