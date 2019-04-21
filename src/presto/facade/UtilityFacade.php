<?php
namespace Presto\Facade;

use Presto\Core\Traits\Singletonable;

class UtilityFacade
{
    use Singletonable;


    /** @return \Presto\Core\Utilities\Arrayer */
    public function arrayer() { return \Presto\Core\Utilities\Arrayer::instance(); }

    /** @return \Presto\Core\Utilities\Collection */
    public function collection(array $rows=[]) { return new \Presto\Core\Utilities\Collection($rows); }

    /** @return \Presto\Core\Utilities\Encrypter */
    public function encrypter() { return \Presto\Core\Utilities\Encrypter::instance(); }

    /** @return \Presto\Core\Utilities\Paginator */
    public function paginator(array $rows=[]) { return new \Presto\Core\Utilities\Paginator($rows); }

    /** @return \Presto\Core\Utilities\Stringer */
    public function stringer() { return \Presto\Core\Utilities\Stringer::instance(); }

    /** @return \Presto\Core\Utilities\Pregular */
    public function pregular() { return \Presto\Core\Utilities\Pregular::instance(); }

    /** @return \Presto\Core\Utilities\Pager */
    public function pager() { return \Presto\Core\Utilities\Pager::instance(); }

    /** @return \Presto\Core\Utilities\Expression */
    public function expression() { return \Presto\Core\Utilities\Expression::instance(); }

    /** @return \Presto\Core\Utilities\Validator */
    public function validator() { return \Presto\Core\Utilities\Validator::instance(); }

    /** @return \Presto\Core\Utilities\FilteringCondition */
    public function condition(array $condition=[]) { return new \Presto\Core\Utilities\FilteringCondition($condition); }

    /** @return \Presto\Core\Utilities\FilteringParameter */
    public function parameter(array $parameter=[]) { return new \Presto\Core\Utilities\FilteringParameter($parameter); }

    /** @return \Presto\Core\Utilities\Breadcrumb */
    public function breadcrumb(array $rows=[]) { return \Presto\Core\Utilities\Breadcrumb::instance()->adds($rows); }

    /** @return \Presto\Core\Utilities\Lottery */
    public function lottery() { return \Presto\Core\Utilities\Lottery::instance(); }

}