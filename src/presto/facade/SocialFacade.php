<?php
namespace Presto\Facade;

use Presto\Core\Traits\Singletonable;

class SocialFacade
{
    use Singletonable;

    /** @return \Presto\Socialite\Google */
    public function google() { return \Presto\Socialite\Google::instance(); }


}