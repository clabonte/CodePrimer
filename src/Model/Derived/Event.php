<?php
/**
 * Created by PhpStorm.
 * User: cbonte
 * Date: 2019-05-19
 * Time: 3:31 PM.
 */

namespace CodePrimer\Model\Derived;

use CodePrimer\Model\DataBundle;

class Event extends DataBundle
{
    public function __construct(string $name, string $description = '')
    {
        parent::__construct($name, $description);
    }
}
