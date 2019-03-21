<?php

namespace Pinq\Queries\Common\Source;

/**
 * Array/iterator value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayOrIterator extends ParameterSourceBase
{
    public function getType()
    {
        return self::ARRAY_OR_ITERATOR;
    }
}
