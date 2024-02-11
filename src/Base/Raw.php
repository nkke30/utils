<?php
declare(strict_types= 1);

namespace Nickimbo\Utils\Base;

/**
 * The Database raw object.
 */
class Raw
{
    /**
     * The array of mapping data for the raw string.
     *
     * @var array
     */
    public $map;

    /**
     * The raw string.
     *
     * @var string
     */
    public $value;
}