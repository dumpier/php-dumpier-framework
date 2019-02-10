<?php
namespace Presto\Helpers\Templates;

use Presto\Utilities\PregUtility;
use Presto\Traits\Singletonable;

class TemplateTag
{
    use Singletonable;


    /**
     * 独自タグをPHPに変換
     * @param string $phtml
     * @return string
     */
    public function convert(string $phtml)
    {
        // includeファイルをロードする
        $phtml = $this->includes($phtml);

        // 変数代入タグを反映する
        $phtml = $this->variables($phtml);

        // 関数タグを反映する
        $phtml = $this->callables($phtml);

        // PHP構文タグ
        $phtml = $this->syntaxes($phtml);

        $phtml = preg_replace("/\?>(\s*)<\?php/", "$1", $phtml);
        return $phtml;
    }


    /**
     * includeファイルをロードする
     * @param string $phtml
     * @throws \Exception
     * @return string
     */
    public function includes(string $phtml)
    {
        // includeタグ一覧
        $includes = PregUtility::getAll("/@include\(.+?\)/", $phtml);

        foreach ($includes as $include)
        {
            // includeするパーツを探す
            $file = preg_replace("/@include\( *'(.+)' *\)/", "$1", $include);
            $file = path("app/views/{$file}.phtml");

            if(! file_exists($file))
            {
                throw new \Exception("include errror ! [{$file}]");
            }

            $include_phtml = file_get_contents($file);
            $pattern = "/" . preg_quote($include, '/') . "/";
            $phtml = preg_replace($pattern, $include_phtml, $phtml);
        }

        return $phtml;
    }


    /**
     * 変数参照をPHPコードに変換
     * @example {{$name}}, {{ $player.exp }}
     * @param string $phtml
     * @return string|mixed
     */
    public function variables(string $phtml)
    {
        // 例）{{ $xxx.yyy }}
        $variables = PregUtility::getAll("/\{\{ *\\$.+? *\}\} */", $phtml);

        foreach ($variables as $variable)
        {
            $variable_name = preg_replace("/\{\{ *(\\$[^ ]+) *\}\}/", "$1", $variable);
            $variable_name = preg_replace("/\.([^\. ]+)/", "['$1']", $variable_name);

            $phtml = preg_replace("/" . preg_quote($variable, '/') . "/", "<?php echo {$variable_name};?>", $phtml);
        }

//        echo "<pre>".htmlspecialchars($phtml, ENT_QUOTES) . "</pre>";
        return $phtml;
    }


    /**
     * 関数の呼び出しをPHPコードに変換
     * @example {@ var_dump($player.name) }
     * @example {@ var_dump([]) }
     * @example {@ Tags::table($players) }
     * @example {@ Tags::test() }
     *
     * @param string $phtml
     * @return string|mixed
     */
    public function callables(string $phtml)
    {
        // 関数の呼び出し一覧 例）{@ debug() }
        $variables = PregUtility::getAll("/\{@ *.+? *\( *.* *\) *\}/", $phtml);

        foreach ($variables as $variable)
        {
            // 関数名
            $function = preg_replace("/\{@ *(.+) *\(.*/", "$1", $variable);

            // 引数一覧
            $arguments = preg_replace("/.+\( *(.*) *\) *\}/", "$1", $variable);

            // 関数の呼び出しをPHPコードに変換
            $phtml = preg_replace("/" . preg_quote($variable, '/') . "/", "<?php {$function}($arguments); ?>", $phtml);
        }

        return $phtml;
    }


    /**
     * PHP構文に変換するタグ一覧
     * */
    const SYNTAX_LIST = [
        "foreach" => ["pattern"=>"/@foreach *\((.+)\) */m", "replace"=>"<?php foreach($1) {  ?>", ],
        "endforeach" => ["pattern"=>"/@endforeach */m", "replace"=>"<?php } ?>", ],

        "for" => ["pattern"=>"/@for *\((.+)\) */m", "replace"=>"<?php for ($1) { ?>", ],
        "endfor" => ["pattern"=>"/@endfor */m", "replace"=>"<?php } ?>", ],

        "if" => ["pattern"=>"/@if *\((.+)\) */m", "replace"=>"<?php if ($1) { ?>", ],
        "elseif" => ["pattern"=>"/@elseif *\((.+)\)/m", "replace"=>"<?php } elseif ($1) { ?>", ],
        "else" => ["pattern"=>"/@else */m", "replace"=>"<?php } else { ?>", ],
        "endif" => ["pattern"=>"/@endif */m", "replace"=>"<?php } ?>", ],

        "continue" => ["pattern"=>"/@continue */m", "replace"=>"<?php continue; ?>", ],
        "break" => ["pattern"=>"/@continue */m", "replace"=>"<?php break; ?>", ],
    ];


    // 独自タグをPHP構文に変換
    public function syntaxes(string $phtml)
    {
        foreach (self::SYNTAX_LIST as $syntax)
        {
            $phtml = $this->syntax($phtml, $syntax['pattern'], $syntax['replace']);
        }

        return $phtml;
    }

    // 独自タグをPHP構文に変換
    public function syntax(string $phtml, string $pattern, string $replace)
    {
        foreach (PregUtility::getAll($pattern, $phtml) as $matche)
        {
            $phtml = str_replace($matche, preg_replace($pattern, $replace, $matche), $phtml);
        }

        return $phtml;
    }

}