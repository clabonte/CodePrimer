<?php

namespace CodePrimer\Tests\Model\Data;

use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\Data\ContextDataBundle;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\DataBundle;
use CodePrimer\Model\Data\ExternalDataBundle;
use CodePrimer\Model\Data\InternalDataBundle;
use CodePrimer\Model\Data\MessageDataBundle;
use CodePrimer\Model\Data\ReturnedDataBundle;
use CodePrimer\Tests\Helper\TestHelper;
use PHPUnit\Framework\TestCase;

class DataBundleTest extends TestCase
{
    /** @var BusinessBundle */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->businessBundle = TestHelper::getSampleBusinessBundle();
    }

    /**
     * @dataProvider dataBundleProvider
     */
    public function testGet(DataBundle $dataBundle)
    {
        $modelName = 'User';
        $fieldName = 'id';
        $user = $this->businessBundle->getBusinessModel($modelName);

        self::assertNull($dataBundle->get($modelName, $fieldName));
        self::assertFalse($dataBundle->isPresent($modelName, $fieldName));

        $dataBundle->add(new Data($user, $user->getField($fieldName)));

        self::assertTrue($dataBundle->isPresent($modelName, $fieldName));

        self::assertNotNull($dataBundle->get($modelName, $fieldName));
        $data = $dataBundle->get($modelName, $fieldName);
        self::assertEquals($modelName, $data->getBusinessModel()->getName());
        self::assertEquals($fieldName, $data->getField()->getName());
    }

    /**
     * @dataProvider dataBundleProvider
     */
    public function testRemove(DataBundle $dataBundle)
    {
        $modelName = 'User';
        $fieldName = 'id';

        $user = $this->businessBundle->getBusinessModel($modelName);

        self::assertNull($dataBundle->get($modelName, $fieldName));
        self::assertFalse($dataBundle->remove($modelName, $fieldName));

        $dataBundle->add(new Data($user, $user->getField($fieldName)));
        self::assertNotNull($dataBundle->get($modelName, $fieldName));
        self::assertTrue($dataBundle->isPresent($modelName, $fieldName));

        self::assertTrue($dataBundle->remove($modelName, $fieldName));
        self::assertNull($dataBundle->get($modelName, $fieldName));
        self::assertFalse($dataBundle->isPresent($modelName, $fieldName));
    }

    public function dataBundleProvider(): array
    {
        return [
            'InternalDataBundle' => [new InternalDataBundle()],
            'ExternalDataBundle' => [new ExternalDataBundle()],
            'ContextDataBundle' => [new ContextDataBundle()],
            'ReturnedDataBundle' => [new ReturnedDataBundle()],
            'MessageDataBundle' => [new MessageDataBundle()],
        ];
    }
}
