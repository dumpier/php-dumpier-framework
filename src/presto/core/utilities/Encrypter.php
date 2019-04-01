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



    public function random(int $length=8)
    {
        $strings =$this->getStrings();
        $range_max = count($strings) - 1;

        $result = null;

        for ($i = 0; $i < $length; $i++)
        {
            $result .= $strings[rand(0, $range_max)];
        }

        return $result;
    }

    private $strings = [];
    private function getStrings()
    {
        if(empty($this->strings))
        {
            $this->strings = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
        }

        return $this->strings;
    }

}