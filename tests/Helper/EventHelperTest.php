<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\EventHelper;
use CodePrimer\Model\Data\Data;
use CodePrimer\Model\Data\EventData;
use CodePrimer\Model\Data\EventDataBundle;
use CodePrimer\Model\Derived\Event;
use PHPUnit\Framework\TestCase;

class EventHelperTest extends TestCase
{
    public function testSetNonConflictingDataNamesShouldUseFieldName()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');

        $event = new Event('Test Event');
        $dataBundle = new EventDataBundle('bundle1');
        $dataBundle
            ->add(new EventData($user, 'created'))
            ->add(new EventData($user, 'updated'));
        $event->addDataBundle($dataBundle);

        $helper = new EventHelper();
        $data = $helper->getNamedData($event);

        self::assertCount(2, $data);
        self::assertArrayHasKey('created', $data);
        self::assertArrayHasKey('updated', $data);
    }

    public function testSetConflictingFieldDataNamesShouldUseModelAndFieldName()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');
        $topic = $bundle->getBusinessModel('Topic');

        $event = new Event('Test Event');
        $dataBundle = new EventDataBundle('bundle1');
        $dataBundle
            ->add(new EventData($user, 'created'))
            ->add(new EventData($user, 'updated'))
            ->add(new EventData($topic, 'updated'));
        $event->addDataBundle($dataBundle);

        $helper = new EventHelper();
        $data = $helper->getNamedData($event);

        self::assertCount(3, $data);
        self::assertArrayHasKey('created', $data);
        self::assertArrayNotHasKey('updated', $data);
        self::assertArrayHasKey('userUpdated', $data);
        self::assertArrayHasKey('topicUpdated', $data);
    }

    public function testSetConflictingModelAndFieldDataNamesShouldUseBundleModelAndFieldName()
    {
        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');
        $topic = $bundle->getBusinessModel('Topic');

        $event = new Event('Test Event');
        $dataBundle = new EventDataBundle('bundle1');
        $dataBundle
            ->add(new EventData($user, 'created'))
            ->add(new EventData($user, 'updated'))
            ->add(new EventData($topic, 'updated'));
        $event->addDataBundle($dataBundle);

        $dataBundle = new EventDataBundle('bundle2');
        $dataBundle
            ->add(new EventData($user, 'created'))
            ->add(new EventData($user, 'updated'))
            ->add(new EventData($topic, 'updated'));
        $event->addDataBundle($dataBundle);

        $helper = new EventHelper();
        $data = $helper->getNamedData($event);

        self::assertCount(6, $data);
        self::assertArrayNotHasKey('created', $data);
        self::assertArrayNotHasKey('updated', $data);
        self::assertArrayNotHasKey('userUpdated', $data);
        self::assertArrayNotHasKey('topicUpdated', $data);
        self::assertArrayNotHasKey('userCreated', $data);

        self::assertArrayHasKey('bundle1UserCreated', $data);
        self::assertArrayHasKey('bundle1UserUpdated', $data);
        self::assertArrayHasKey('bundle1TopicUpdated', $data);
        self::assertArrayHasKey('bundle2UserCreated', $data);
        self::assertArrayHasKey('bundle2UserUpdated', $data);
        self::assertArrayHasKey('bundle2TopicUpdated', $data);
    }

    public function testListMandatoryFieldsShouldReturnProperFields()
    {
        $helper = new EventHelper();
        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');

        $event = $bundle->getEvent('Registration Request');
        self::assertNotNull($event);

        $list = $helper->getMandatoryData($event);
        self::assertCount(2, $list);
        $this->assertDataPresent(new Data($user, 'email', Data::ATTRIBUTES), $list);
        $this->assertDataPresent(new Data($user, 'password', Data::ATTRIBUTES), $list);
    }

    /**
     * @param Data[] $list
     */
    private function assertDataPresent(Data $expectedData, array $list)
    {
        $found = false;

        foreach ($list as $data) {
            if ($data->isSame($expectedData)) {
                $found = true;
            }
        }
        self::assertTrue($found, 'Data not found: '.$expectedData->getBusinessModel()->getName().', '.$expectedData->getField()->getName());
    }
}
