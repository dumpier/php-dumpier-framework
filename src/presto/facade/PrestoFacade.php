<?php
namespace Presto\Facade;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Presto;

class PrestoFacade
{
    use Singletonable;

    /** @return string */
    public function baseuri() { return Presto::instance()->baseuri(); }
    /** @return string */
    public function domain() { return Presto::instance()->domain(); }
    /** @return string */
    public function protocol() { return Presto::instance()->protocol(); }

    /** @return \Presto\Core\Protocols\Http */
    public function http() { return \Presto\Core\Protocols\Http::instance(); }


    /** @return \Presto\Core\Routing */
    public function routing() { return \Presto\Core\Routing::instance(); }

    /** @return \Presto\Core\Response */
    public function response() { return \Presto\Core\Response::instance(); }

    /** @return \Presto\Core\Request */
    public function request() { return \Presto\Core\Request::instance(); }

    /** @return mixed */
    public function input(string $name="", $default=null) { return \Presto\Core\Request::instance()->input($name, $default); }


    // view
    /** @return \Presto\Core\Views\View */
    public function view() { return \Presto\Core\Views\View::instance(); }

    /** @return \Presto\Core\Views\TemplateEngine */
    public function template() { return \Presto\Core\Views\TemplateEngine::instance(); }

    // session
    /** @return \Presto\Core\Session */
    public function session() { return \Presto\Core\Session::instance(); }


}
