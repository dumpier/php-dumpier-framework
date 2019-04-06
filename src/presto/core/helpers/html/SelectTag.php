<?php
namespace Presto\Core\Helpers\Html;

use Presto\Core\Traits\Singletonable;

class SelectTag extends BaseTag
{
    use Singletonable;

    /** 選択状態の値 */
    protected $default = NULL;

    protected $title;
    protected $prepend;
    protected $append;

    public function default(string $value)
    {
        $this->default = $value;
        return $this;
    }

    public function title(string $value="選択")
    {
        $this->title = $value;
        return $this;
    }

    public function prepend(string $value)
    {
        $this->prepend = $value;
        return $this;
    }

    public function append(string $value)
    {
        $this->append = $value;
        return $this;
    }

    public function render(string $tagname, array $rows, $default=NULL)
    {
        echo "<select name={$tagname} class=\"form-control\">";

        if($this->title)
        {
            echo "<option value=0>-- {$this->title} --</option>";
        }

        foreach ($rows as $key=>$val)
        {
            echo "<option value=\"{$key}\">{$this->prepend}{$val}{$this->append}</option>";
        }

        echo "</select>";
    }


}