<?php
// utilities
if(! function_exists('str')) { /** @return \Presto\Utilities\StringUtility */ function str() { return \Presto\Utilities\StringUtility::getInstance(); }}
if(! function_exists('arr')) { /** @return \Presto\Utilities\ArrayUtility */ function arr() { return \Presto\Utilities\ArrayUtility::getInstance(); }}
if(! function_exists('compare')) { /** @return \Presto\Utilities\CompareUtility */ function compare() { return \Presto\Utilities\CompareUtility::getInstance(); }}

// collection
if(! function_exists('collection')) { /** @return \Presto\Collection */ function collection(array $rows=[]) { return new \Presto\Collection($rows); }}


if(! function_exists('app')){
    function app(string $class, ...$parameters) {
        if(class_exists($class)) { return empty($parameters[0]) ? new $class() : new $class($parameters[0]); }
        throw new Exception("クラス参照エラー[{$class}]");
    }
}


// path
if(! function_exists('path')) {
    function path(string $sub=null) {
        $path = (dirname(dirname(dirname(dirname(__FILE__)))));
        if($sub) { $path .= DIRECTORY_SEPARATOR.$sub; }
        return str()->cleanDirectorySeparator($path);
    }
}
if(! function_exists('class_path')) { function class_path() { return path('app/classes'); } }
if(! function_exists('service_path')) { function service_path() { return path('app/classes/services'); } }
if(! function_exists('repository_path')) { function repository_path() { return path('app/classes/models/repositories'); } }


if(! function_exists('config')) { function config(string $filename, string $key="") { return \Presto\Files\ConfigLoader::getInstance()->get($filename, $key); }}
if(! function_exists('routing')) { /** @return \Presto\Http\Routing */ function routing(string $uri=null) { return \Presto\Http\Routing::getInstance(); }}
if(! function_exists('request')){ /** @return \Presto\Http\Request */ function request() { return \Presto\Http\Request::getInstance(); }}
if(! function_exists('response')){ /** @return \Presto\Http\Response */ function response(string $uri=null) { return \Presto\Http\Response::getInstance(); }}
if(! function_exists('view')) { /** @return \Presto\Mvc\View */ function view(string $path=null) { return \Presto\Mvc\View::getInstance(); }}
if(! function_exists('handler')) { /** @return \App\Exceptions\Handler */ function handler() { return \App\Exceptions\Handler::getInstance(); }}
if(! function_exists('input')) {
    /** @return \Presto\Consoles\ArgvInput|mixed */
    function input(string $name, $default=null) {
        $instance = \Presto\Consoles\ArgvInput::getInstance();
        return empty($name) ? $instance : $instance->value($name, $default);
    }
}


// tags
if(! function_exists('template')) { /** @return \Presto\Helpers\Templates\TemplateTag */ function template() { return \Presto\Helpers\Templates\TemplateTag::getInstance(); }}
if(! function_exists('html')) { /** @return \Presto\Helpers\Html\HtmlTag */ function html() { return \Presto\Helpers\Html\HtmlTag::getInstance(); }}


// files
// directory
if(! function_exists('directory')) { /** @return \Presto\Files\DirectoryLoader */ function directory() { return \Presto\Files\DirectoryLoader::getInstance(); }}
// csv
if(! function_exists('csv')) { /** @return \Presto\Files\CsvLoader */ function csv() { return \Presto\Files\CsvLoader::getInstance(); }}
// paging
if(! function_exists('paging')) { /** @return \Presto\Helpers\Paging */ function paging() { return \Presto\Helpers\Paging::getInstance(); }}


// debugbar
if(! function_exists('debugbar')) { /** @return \Presto\Debugbar\Debugbar */ function debugbar() { return \Presto\Debugbar\Debugbar::getInstance(); }}
if(! function_exists('timelines')) { function timelines( string $msg="", array $data=[] ) { return \Presto\Debugbar\Debugbar::getInstance()->timelines($msg, $data); }}
if(! function_exists('messages')) { function messages( string $msg="", array $data=[] ) { return \Presto\Debugbar\Debugbar::getInstance()->messages($msg, $data); }}


// database
if(! function_exists('where')) { /** @return mixed */ function where(array $conditions=[]) { return \Presto\Databases\ArrayToWhere::getInstance()->convert( $conditions ); }}
if(! function_exists('database')) {
    /** @return \Presto\Databases\QueryBuilder */
    function database(string $name="", string $database="") {
        if(empty($name) && empty($database))
        {
            return \Presto\Databases\QueryBuilder::getInstance();
        }
        return \Presto\Databases\QueryBuilder::getInstance()->connect($name, $database);
    }
}
if(! function_exists('select')) { /** @return mixed */ function select(string $query, array $binds=[], $name=null) { return \Presto\Databases\QueryBuilder::getInstance()->select($query, $binds, $name); }}




