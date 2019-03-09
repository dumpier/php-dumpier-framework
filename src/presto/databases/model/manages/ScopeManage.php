<?php
namespace Presto\Databases\Model\Manages;


/**
 *
 * TODO 下位リレーションロードする際、再帰的に指定パラメータを渡したい
 *
 *
 */
class ScopeManage
{
    protected $scopes;

    public function __construct(array $scopes)
    {
        $this->scopes = $scopes;
    }

}