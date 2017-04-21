<?php
namespace Industrious\WpHelpers\Transformers;

/**
 * Class BaseTransformer
 */
abstract class BaseTransformer
{
    /**
     * @param $item
     *
     * @return mixed
     */
    abstract public function transform($item);

    /**
     * @param array $items
     *
     * @return array
     */
    public function transformArray(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }
}
