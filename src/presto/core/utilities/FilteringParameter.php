<?php
namespace Presto\Core\Utilities;

use Presto\Core\Traits\Accessible;
use Presto\Core\Helpers\Html\PagerTag;

class FilteringParameter
{
    use Accessible;

    /** @var integer ページ番号 */
    protected $page = 1;

    /** @var integer OFFSET */
    protected $offset = 0;

    /** @var integer LIMIT */
    protected $limit = PagerTag::LIMIT_COUNT;

    /** @var array 並び替え */
    protected $order = [];

    /** @var array グルーピング条件 */
    protected $group = [];

    /** @var array 抽出項目一覧 */
    protected $fields = [];

    /** @var string COUNT()する項目名 */
    protected $count_field = "*";

    /** @var FilteringCondition */
    protected $condition;


    public function page(int $input=0)
    {
        return $this->accessor("page", $input);
    }

    public function offset(int $input=0)
    {
        return $this->accessor("offset", $input);
    }

    public function limit(int $input=0)
    {
        return $this->accessor("limit", $input);
    }

    public function order(array $input=[])
    {
        return $this->accessor("order", $input);
    }

    public function group(array $input=[])
    {
        return $this->accessor("group", $input);
    }

    public function fields(array $input=[])
    {
        return $this->accessor("fields", $input);
    }

    public function count_filed(string $input)
    {
        return $this->accessor("count_filed", $input);
    }

    public function condition(array $input=[])
    {
        return $this->accessor("condition", $input);
    }

}