<?php

namespace CodePrimer\Tests\Model\Data;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class DataBundleTest extends TestCase
{
    /** @var DataBundle */
    private $dataBundle;

    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataBundle = new DataBundle();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    public function testGet()
    {
        $modelName = 'User';
        $fieldName = 'id';
        $user = $this->businessBundle->getBusinessModel($modelName);

        self::assertNull($this->dataBundle->get($modelName, $fieldName));
        self::assertFalse($this->dataBundle->isPresent($modelName, $fieldName));

        $this->dataBundle->add(new Data($user, $user->getField($fieldName)));

        self::assertTrue($this->dataBundle->isPresent($modelName, $fieldName));

        self::assertNotNull($this->dataBundle->get($modelName, $fieldName));
        $data = $this->dataBundle->get($modelName, $fieldName);
        self::assertEquals($modelName, $data->getBusinessModel()->getName());
        self::assertEquals($fieldName, $data->getField()->getName());
    }

    public function testRemove()
    {
        $modelName = 'User';
        $fieldName = 'id';

        $user = $this->businessBundle->getBusinessModel($modelName);

        self::assertNull($this->dataBundle->get($modelName, $fieldName));
        self::assertFalse($this->dataBundle->remove($modelName, $fieldName));

        $this->dataBundle->add(new Data($user, $user->getField($fieldName)));
        self::assertNotNull($this->dataBundle->get($modelName, $fieldName));
        self::assertTrue($this->dataBundle->isPresent($modelName, $fieldName));

        self::assertTrue($this->dataBundle->remove($modelName, $fieldName));
        self::assertNull($this->dataBundle->get($modelName, $fieldName));
        self::assertFalse($this->dataBundle->isPresent($modelName, $fieldName));
    }
}
