<?php
if(! function_exists("arrayer")) { /** @return \Presto\Arrayer */ function arrayer() { return \Presto\Arrayer::instance(); }}
if(! function_exists("collection")) { /** @return \Presto\Collection */ function collection(array $rows=[]) { return new \Presto\Collection($rows); }}
if(! function_exists("paginator")) { /** @return \Presto\Paginator */ function paginator(array $rows=[], int $total_count=0, int $page=1, int $limit=\Presto\Paging::LIMIT_COUNT) { return new \Presto\Paginator($rows, $total_count, $page, $limit); }}
if(! function_exists("stringer")) { /** @return \Presto\Stringer */ function stringer() { return \Presto\Stringer::instance(); }}
if(! function_exists("pregular")) { /** @return \Presto\Pregular */ function pregular() { return \Presto\Pregular::instance(); }}
if(! function_exists("expression")) { /** @return \Presto\Expression */ function expression() { return \Presto\Expression::instance(); }}
if(! function_exists("validator")) { /** @return \Presto\Validator */ function validator() { return \Presto\Validator::instance(); }}
if(! function_exists("routing")) { /** @return \Presto\Routing */ function routing() { return \Presto\Routing::instance(); }}
if(! function_exists("response")){ /** @return \Presto\Response */ function response(string $uri=null) { return \Presto\Response::instance(); }}
if(! function_exists("request")){ /** @return \Presto\Request */ function request() { return \Presto\Request::instance(); }}
if(! function_exists("input")) { /** @return mixed */ function input(string $name="", $default=null) { return \Presto\Request::instance()->input($name, $default); } }
if(! function_exists("condition")) { /** @return \Presto\FilteringCondition */ function condition(array $condition=[]) { return \Presto\FilteringCondition($condition); }}
if(! function_exists("parameter")) { /** @return \Presto\FilteringParameter */ function parameter(array $parameter=[]) { return \Presto\FilteringParameter($parameter); }}

// view
if(! function_exists("view")) { /** @return \Presto\View */ function view(string $path=null) { return \Presto\View::instance(); }}
if(! function_exists("template")) { /** @return \Presto\TemplateEngine */ function template() { return \Presto\TemplateEngine::instance(); }}
if(! function_exists("html")) { /** @return \Presto\HtmlTag */ function html() { return \Presto\HtmlTag::instance(); }}
if(! function_exists("paging")) { /** @return \Presto\Paging */ function paging() { return \Presto\Paging::instance(); }}
if(! function_exists("breadcrumb")) { /** @return \Presto\Breadcrumb */ function breadcrumb(array $breadcrumbs=[]) { return \Presto\Breadcrumb::instance()->adds($breadcrumbs); }}

if(! function_exists("baseuri")){ /** @return string */ function baseuri() { return config("app", "baseuri"); }}
if(! function_exists("host")){ /** @return string */ function domain() { return $_SERVER['HTTP_HOST']; }}
if(! function_exists("protocol")){ /** @return string */ function protocol() { return $_SERVER["SERVER_PROTOCOL"]; }}

// app
if(! function_exists("app")){
    function app(string $class, ...$parameters) {
        if(class_exists($class)) { return \ReflectionClass($class, $parameters); }
        throw new Exception("クラス参照エラー[{$class}]");
    }
}


// path
if(! function_exists("path")) { function path(string $path="") { return stringer()->cleanDirectorySeparator(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . $path); }}
if(! function_exists("framework_path")) { function framework_path(string $path="") { return stringer()->cleanDirectorySeparator(__DIR__ . DIRECTORY_SEPARATOR . $path); }}
if(! function_exists("app_path")) { function app_path(string $path="") { return path("app/{$path}"); } }
if(! function_exists("class_path")) { function class_path(string $path="") { return path("app/classes/{$path}"); } }
if(! function_exists("controller_path")) { function controller_path(string $path="") { return path("app/classes/http/controllers/{$path}"); } }
if(! function_exists("service_path")) { function service_path(string $path="") { return path("app/classes/services/{$path}"); } }
if(! function_exists("repository_path")) { function repository_path(string $path="") { return path("app/classes/models/repositories/{$path}"); } }
if(! function_exists("view_path")) { function view_path(string $path="") { return path("app/views/{$path}"); } }
if(! function_exists("storage_path")) { function storage_path(string $path="") { return path("storages/{$path}"); } }


if(! function_exists("handler")) { /** @return \App\Exceptions\Handler */ function handler() { return \App\Exceptions\Handler::instance(); }}


// files
if(! function_exists("config")) { function config(string $filename, string $key="") { return \Presto\Files\ConfigLoader::instance()->get($filename, $key); }}
if(! function_exists("directory")) { /** @return \Presto\Files\DirectoryLoader */ function directory() { return \Presto\Files\DirectoryLoader::instance(); }}
if(! function_exists("csv")) { /** @return \Presto\Files\CsvLoader */ function csv() { return \Presto\Files\CsvLoader::instance(); }}


// debugbar
if(! function_exists("debugbar")) { /** @return \Presto\Debugbar */ function debugbar() { return \Presto\Debugbar::instance(); }}
if(! function_exists("timelines")) { function timelines( string $msg="", array $data=[] ) { return \Presto\Debugbar::instance()->timelines($msg, $data); }}
if(! function_exists("messages")) { function messages( string $msg="", array $data=[] ) { return \Presto\Debugbar::instance()->messages($msg, $data); }}


// database
if(! function_exists("where")) { /** @return mixed */ function where(array $conditions=[]) { return \Presto\QueryToWhere::instance()->convert( $conditions ); }}
if(! function_exists("database")) {
    /** @return \Presto\QueryBuilder */
    function database(string $name="", string $database="") {
        if(empty($name) && empty($database))
        {
            return \Presto\QueryBuilder::instance();
        }
        return \Presto\QueryBuilder::instance()->connect($name, $database);
    }
}
if(! function_exists("select")) { /** @return mixed */ function select(string $query, array $binds=[], $name=null) { return \Presto\QueryBuilder::instance()->select($query, $binds, $name); }}

