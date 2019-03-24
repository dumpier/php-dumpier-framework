<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Singletonable;

class Encrypter
{
    use Singletonable;


    /**
     * パスワードをハッシュ化する
     * @param string $password
     * @return string
     */
    public function password(string $password)
    {
        return $this->sha256($password);
    }


    /**
     * SHA-256
     * @param string $value
     * @return string
     */
    public function sha256(string $value)
    {
        return hash('sha256', $value);
    }

}