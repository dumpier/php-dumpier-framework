<?php
if(! function_exists("arrayer")) { /** @return \Presto\Utilities\Arrayer */ function arrayer() { return \Presto\Utilities\Arrayer::instance(); }}
if(! function_exists("collection")) { /** @return \Presto\Utilities\Collection */ function collection(array $rows=[]) { return new \Presto\Utilities\Collection($rows); }}
if(! function_exists("paginator")) { /** @return \Presto\Utilities\Paginator */ function paginator(array $rows=[], int $total_count=0, int $page=1, int $limit=\Presto\Paging::LIMIT_COUNT) { return new \Presto\Utilities\Paginator($rows, $total_count, $page, $limit); }}
if(! function_exists("stringer")) { /** @return \Presto\Utilities\Stringer */ function stringer() { return \Presto\Utilities\Stringer::instance(); }}
if(! function_exists("pregular")) { /** @return \Presto\Utilities\Pregular */ function pregular() { return \Presto\Utilities\Pregular::instance(); }}
if(! function_exists("expression")) { /** @return \Presto\Utilities\Expression */ function expression() { return \Presto\Utilities\Expression::instance(); }}
if(! function_exists("validator")) { /** @return \Presto\Utilities\Validator */ function validator() { return \Presto\Utilities\Validator::instance(); }}
if(! function_exists("condition")) { /** @return \Presto\Utilities\FilteringCondition */ function condition(array $condition=[]) { return \Presto\Utilities\FilteringCondition($condition); }}
if(! function_exists("parameter")) { /** @return \Presto\Utilities\FilteringParameter */ function parameter(array $parameter=[]) { return \Presto\Utilities\FilteringParameter($parameter); }}
if(! function_exists("breadcrumb")) { /** @return \Presto\Utilities\Breadcrumb */ function breadcrumb(array $rows=[]) { return \Presto\Utilities\Breadcrumb::instance()->adds($rows); }}
// debugbar
if(! function_exists("debugbar")) { /** @return \Presto\Utilities\Debugbar */ function debugbar() { return \Presto\Utilities\Debugbar::instance(); }}
if(! function_exists("timelines")) { function timelines( string $msg="", array $data=[] ) { return \Presto\Utilities\Debugbar::instance()->timelines($msg, $data); }}
if(! function_exists("messages")) { function messages( string $msg="", array $data=[] ) { return \Presto\Utilities\Debugbar::instance()->messages($msg, $data); }}


if(! function_exists("routing")) { /** @return \Presto\Routing */ function routing() { return \Presto\Routing::instance(); }}
if(! function_exists("response")){ /** @return \Presto\Response */ function response(string $uri=null) { return \Presto\Response::instance(); }}
if(! function_exists("request")){ /** @return \Presto\Request */ function request() { return \Presto\Request::instance(); }}
if(! function_exists("input")) { /** @return mixed */ function input(string $name="", $default=null) { return \Presto\Request::instance()->input($name, $default); } }


// view
if(! function_exists("view")) { /** @return \Presto\Views\View */ function view(string $path=null) { return \Presto\Views\View::instance(); }}
if(! function_exists("template")) { /** @return \Presto\Views\TemplateEngine */ function template() { return \Presto\Views\TemplateEngine::instance(); }}

// helper
if(! function_exists("html")) { /** @return \Presto\Helpers\Html\HtmlTag */ function html() { return \Presto\Helpers\Html\HtmlTag::instance(); }}
if(! function_exists("paging")) { /** @return \Presto\Helpers\Html\Paging */ function paging() { return \Presto\Helpers\Html\Paging::instance(); }}

if(! function_exists("baseuri")){ /** @return string */ function baseuri() { return config("app", "baseuri"); }}
if(! function_exists("host")){ /** @return string */ function domain() { return $_SERVER['HTTP_HOST']; }}
if(! function_exists("protocol")){ /** @return string */ function protocol() { return $_SERVER["SERVER_PROTOCOL"]; }}

// app
if(! function_exists("app")){ /** @return object */ function app(string $class, ...$parameters) { if(class_exists($class)) { return new $class(...$parameters); } throw new Exception("クラス参照エラー[{$class}]"); }}


// path
if(! function_exists("path")) { /** @return string */ function path(string $path="") { return stringer()->cleanDirectorySeparator(dirname(dirname(dirname(__DIR__))) . "/{$path}"); }}
// if(! function_exists("path")) { /** @return string */ function path(string $path="") { return stringer()->cleanDirectorySeparator(dirname(__DIR__) . "/php-presto-app/{$path}"); }}

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


if(! function_exists("handler")) { /** @return \App\Exceptions\Handler */ function handler() { return \App\Exceptions\Handler::instance(); }}


// files
if(! function_exists("config")) { function config(string $filename, string $key="") { return \Presto\Utilities\Files\ConfigLoader::instance()->get($filename, $key); }}
if(! function_exists("directory")) { /** @return \Presto\Utilities\Files\DirectoryLoader */ function directory() { return \Presto\Utilities\Files\DirectoryLoader::instance(); }}
if(! function_exists("csv")) { /** @return \Presto\Utilities\Files\CsvLoader */ function csv() { return \Presto\Utilities\Files\CsvLoader::instance(); }}




// database
if(! function_exists("where")) { /** @return mixed */ function where(array $conditions=[]) { return \Presto\Databases\QueryToWhere::instance()->convert( $conditions ); }}
if(! function_exists("database")) {
    /** @return \Presto\Databases\QueryBuilder */
    function database(string $name="", string $database="") {
        if(empty($name) && empty($database))
        {
            return \Presto\QueryBuilder::instance();
        }
        return \Presto\Databases\QueryBuilder::instance()->connect($name, $database);
    }
}
if(! function_exists("select")) { /** @return mixed */ function select(string $query, array $binds=[], $name=null) { return \Presto\Databases\QueryBuilder::instance()->select($query, $binds, $name); }}

