<?php
// utilities
if(! function_exists("arrayer")) { /** @return \Presto\Core\Utilities\Arrayer */ function arrayer() { return \Presto\Core\Utilities\Arrayer::instance(); }}
if(! function_exists("collection")) { /** @return \Presto\Core\Utilities\Collection */ function collection(array $rows=[]) { return new \Presto\Core\Utilities\Collection($rows); }}
if(! function_exists("encrypter")) { /** @return \Presto\Core\Utilities\Encrypter */ function encrypter() { return \Presto\Core\Utilities\Encrypter::instance(); }}
if(! function_exists("paginator")) { /** @return \Presto\Core\Utilities\Paginator */ function paginator(array $rows=[], int $total_count=0, int $page=1, int $limit=\Presto\Core\Helpers\Html\Paging::LIMIT_COUNT) { return new \Presto\Core\Utilities\Paginator($rows, $total_count, $page, $limit); }}
if(! function_exists("stringer")) { /** @return \Presto\Core\Utilities\Stringer */ function stringer() { return \Presto\Core\Utilities\Stringer::instance(); }}
if(! function_exists("pregular")) { /** @return \Presto\Core\Utilities\Pregular */ function pregular() { return \Presto\Core\Utilities\Pregular::instance(); }}
if(! function_exists("expression")) { /** @return \Presto\Core\Utilities\Expression */ function expression() { return \Presto\Core\Utilities\Expression::instance(); }}
if(! function_exists("validator")) { /** @return \Presto\Core\Utilities\Validator */ function validator() { return \Presto\Core\Utilities\Validator::instance(); }}
if(! function_exists("condition")) { /** @return \Presto\Core\Utilities\FilteringCondition */ function condition(array $condition=[]) { return new \Presto\Core\Utilities\FilteringCondition($condition); }}
if(! function_exists("parameter")) { /** @return \Presto\Core\Utilities\FilteringParameter */ function parameter(array $parameter=[]) { return new \Presto\Core\Utilities\FilteringParameter($parameter); }}
if(! function_exists("breadcrumb")) { /** @return \Presto\Core\Utilities\Breadcrumb */ function breadcrumb(array $rows=[]) { return \Presto\Core\Utilities\Breadcrumb::instance()->adds($rows); }}
// debugbar
if(! function_exists("debugbar")) { /** @return \Presto\Core\Utilities\Debugbar */ function debugbar() { return \Presto\Core\Utilities\Debugbar::instance(); }}
if(! function_exists("timelines")) { function timelines( string $msg="", array $data=[] ) { return \Presto\Core\Utilities\Debugbar::instance()->timelines($msg, $data); }}
if(! function_exists("messages")) { function messages( string $msg="", array $data=[] ) { return \Presto\Core\Utilities\Debugbar::instance()->messages($msg, $data); }}


if(! function_exists("routing")) { /** @return \Presto\Core\Routing */ function routing() { return \Presto\Core\Routing::instance(); }}
if(! function_exists("response")){ /** @return \Presto\Core\Response */ function response() { return \Presto\Core\Response::instance(); }}
if(! function_exists("request")){ /** @return \Presto\Core\Request */ function request() { return \Presto\Core\Request::instance(); }}
if(! function_exists("input")) { /** @return mixed */ function input(string $name="", $default=null) { return \Presto\Core\Request::instance()->input($name, $default); } }


// view
if(! function_exists("view")) { /** @return \Presto\Core\Views\View */ function view() { return \Presto\Core\Views\View::instance(); }}
if(! function_exists("template")) { /** @return \Presto\Core\Views\TemplateEngine */ function template() { return \Presto\Core\Views\TemplateEngine::instance(); }}

// helper
if(! function_exists("html")) { /** @return \Presto\Core\Helpers\Html\HtmlTag */ function html() { return \Presto\Core\Helpers\Html\HtmlTag::instance(); }}
if(! function_exists("paging")) { /** @return \Presto\Core\Helpers\Html\Paging */ function paging() { return \Presto\Core\Helpers\Html\Paging::instance(); }}

if(! function_exists("baseuri")){ /** @return string */ function baseuri() { return config("app", "baseuri"); }}
if(! function_exists("domain")){ /** @return string */ function domain() { return $_SERVER['HTTP_HOST']; }}
if(! function_exists("protocol")){ /** @return string */ function protocol() { return $_SERVER["SERVER_PROTOCOL"]; }}

// app
if(! function_exists("app")){ /** @return object */ function app(string $class, ...$parameters) { if(class_exists($class)) { return new $class(...$parameters); } throw new Exception("クラス参照エラー[{$class}]"); }}

// path
if(! function_exists("path")) { /** @return string */ function path(string $path="") {
    // TODO 通常composerで取り込まれた場合とフレームワーク開発のためシンボリックで参照される場合の切り分け
    $realpath = (is_link("/vagrant/code/github/dumpieer/php-presto-app/vendor/dumpier/php-presto-framework")) ? dirname(__DIR__) . "/php-presto-app/{$path}" : dirname(dirname(dirname(__DIR__))) . "/{$path}";
    return stringer()->cleanDirectorySeparator($realpath);
    }
}

if(! function_exists("framework_path")) { /** @return string */ function framework_path(string $path="") { return stringer()->cleanDirectorySeparator(__DIR__ . DIRECTORY_SEPARATOR . $path); }}
if(! function_exists("config_path")) { /** @return string */ function config_path(string $path="") { return path("config/{$path}"); } }
if(! function_exists("app_path")) { /** @return string */ function app_path(string $path="") { return path("app/{$path}"); } }
if(! function_exists("class_path")) { /** @return string */ function class_path(string $path="") { return path("app/classes/{$path}"); } }
if(! function_exists("resource_path")) { /** @return string */ function resource_path(string $path="") { return path("app/resources/{$path}"); } }
if(! function_exists("controller_path")) { /** @return string */ function controller_path(string $path="") { return path("app/classes/http/controllers/{$path}"); } }
if(! function_exists("service_path")) { /** @return string */ function service_path(string $path="") { return path("app/classes/services/{$path}"); } }
if(! function_exists("repository_path")) { /** @return string */ function repository_path(string $path="") { return path("app/classes/models/repositories/{$path}"); } }
if(! function_exists("template_path")) { /** @return string */ function template_path(string $path="") { return path("app/templates/{$path}"); } }
if(! function_exists("storage_path")) { /** @return string */ function storage_path(string $path="") { return path("storages/{$path}"); } }
if(! function_exists("cache_path")) { /** @return string */ function cache_path(string $path="") { return path("storages/cache/{$path}"); } }
if(! function_exists("cache_template_path")) { /** @return string */ function cache_template_path(string $path="") { return path("storages/cache/templates/{$path}"); } }


// files
if(! function_exists("config")) { function config(string $filename, string $key="") { return \Presto\Core\Utilities\Files\ConfigLoader::instance()->get($filename, $key); }}
if(! function_exists("directory")) { /** @return \Presto\Core\Utilities\Files\DirectoryLoader */ function directory() { return \Presto\Core\Utilities\Files\DirectoryLoader::instance(); }}
if(! function_exists("csv")) { /** @return \Presto\Core\Utilities\Files\CsvLoader */ function csv() { return \Presto\Core\Utilities\Files\CsvLoader::instance(); }}


// database
if(! function_exists("where")) { /** @return mixed */ function where(array $conditions=[]) { return \Presto\Core\Databases\QueryToWhere::instance()->convert( $conditions ); }}
if(! function_exists("database")) {
    /** @return \Presto\Core\Databases\QueryBuilder */
    function database(string $name="", string $database="") {
        if(empty($name) && empty($database))
        {
            return \Presto\Core\Databases\QueryBuilder::instance();
        }
        return \Presto\Core\Databases\QueryBuilder::instance()->connect($name, $database);
    }
}
if(! function_exists("select")) { /** @return mixed */ function select(string $query, array $binds=[], $name=null) { return \Presto\Core\Databases\QueryBuilder::instance()->select($query, $binds, $name); }}


// protocols
if(! function_exists("http")) { /** @return \Presto\Core\Protocols\Http */ function http() { return \Presto\Core\Protocols\Http::instance(); }}


// session
if(! function_exists("session")) { /** @return \Presto\Core\Session */ function session() { return \Presto\Core\Session::instance(); }}
// OAuth
// TODO 現在googleのみ
if(! function_exists("oauth")) { /** @return \Presto\Oauth\Google */ function oauth(string $name) { return \Presto\Oauth\Google::instance(); }}

