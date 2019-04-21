<?php
namespace Presto\Facade;

use Presto\Core\Traits\Singletonable;
use Presto\Core\Helpers\Html\TableTag;
use Presto\Core\Helpers\Html\SelectTag;
use Presto\Core\Helpers\Html\TreeTag;
use Presto\Core\Helpers\Html\PagerTag;
use Presto\Core\Helpers\Html\HtmlTag;

class HtmlFacade
{
    use Singletonable;

    /** @return HtmlTag */
    public function html() { return HtmlTag::instance(); }

    /** @return TableTag */
    public function table() { return TableTag::instance(); }

    /** @return SelectTag */
    public function select() { return SelectTag::instance(); }

    /** @return TreeTag */
    public function tree() { return TreeTag::instance(); }

    /** @return PagerTag */
    public function pager() { return PagerTag::instance(); }
}