<?php
namespace Presto\Core\Helpers\Html;

class BaseTag
{
    /** タグ名 */
    protected $name = "";

    /** CSSのiD */
    protected $tagid = "";

    /** CSSクラス名 */
    protected $css = "";

    /** StyleSheet */
    protected $style = "";

    /** 表示する要素 */
    protected $target = "";

    protected $width = NULL;
    protected $height = NULL;


    /**
     * 属性のリセット
     * @param mixed ...$properties
     * @return $this
     */
    public function reset(...$properties)
    {
        foreach ($properties as $property)
        {
            if(property_exists($this, $property))
            {
                $this->{$property} = NULL;
            }
        }

        return $this;
    }


    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function tagid(string $tagid)
    {
        $this->tagid = $tagid;
        return $this;
    }

    public function css(string $css_class)
    {
        $this->css = $css_class;
        return $this;
    }

    public function style(string $style)
    {
        $this->style = $style;
        return $this;
    }

    public function target(string $target)
    {
        $this->target = $target;
        return $this;
    }

    public function width(int $width)
    {
        $this->width = $width;
        return $this;
    }

    public function height(int $height)
    {
        $this->height = $height;
        return $this;
    }


}