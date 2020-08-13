<?php

namespace CodePrimer\Tests\Helper;

use CodePrimer\Helper\DataNameHelper;
use CodePrimer\Model\Data\Data;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DataNameHelperTest extends TestCase
{
    public function testAssignDataNameWithDuplicateBundleModelAndFieldThrowsException()
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Failed to assign a unique name to data for Bundle: bundle1, Model: User, Field: id. Please consider changing the bundle name');

        $bundle = TestHelper::getSampleBusinessBundle();
        $user = $bundle->getBusinessModel('User');

        $nameHelper = new DataNameHelper();

        $data1 = new Data($user, 'id');
        $data2 = new Data($user, 'id');

        // Assigning a name the first time should work and assign a simple name based on the field only: 'id'
        $conflicts = $nameHelper->assignDataName('bundle1', $data1);
        self::assertEmpty($conflicts);
        self::assertEquals('id', $data1->getName());

        // The second attempt should also work and assign a name based on the model + field: 'userId'
        // And return a conflict on the 'id' name to handle
        $conflicts = $nameHelper->assignDataName('bundle1', $data2);
        self::assertEquals('userId', $data2->getName(), 'Data2 name has not been properly set');
        self::assertCount(1, $conflicts);
        $conflictingName = $conflicts[0];
        self::assertEquals('id', $conflictingName);

        // We should be able to retrieve data1 from this name and it should not have been touched
        $conflictingData = $nameHelper->getData($conflictingName);
        self::assertEquals($data1, $conflictingData);
        self::assertEquals('id', $data1->getName(), 'Data1 should not have changed');
        $conflictingBundle = $nameHelper->getBundleName($conflictingName);
        self::assertEquals('bundle1', $conflictingBundle);

        // Trying to resolve the naming conflict on data1 should work and assign it a name based on bungle + model + field: 'bundle1UserId'
        // And return 2 conflicts: 'id' and 'userId'
        $conflicts = $nameHelper->assignDataName('bundle1', $conflictingData);
        self::assertEquals('bundle1UserId', $data1->getName());
        self::assertEquals('userId', $data2->getName(), 'Data2 should not have changed');
        self::assertCount(2, $conflicts);
        $conflictingName1 = $conflicts[0];
        self::assertEquals('id', $conflictingName1);
        $conflictingName2 = $conflicts[1];
        self::assertEquals('userId', $conflictingName2);

        // The first conflict should point back to data 1
        $conflictingData = $nameHelper->getData($conflictingName1);
        self::assertEquals($data1, $conflictingData);
        $conflictingBundle = $nameHelper->getBundleName($conflictingName);
        self::assertEquals('bundle1', $conflictingBundle);

        // Trying to resolve the first conflict should not change anything and should not return any conflict either
        $conflicts = $nameHelper->assignDataName('bundle1', $conflictingData);
        self::assertEmpty($conflicts, 'There should not be any conflict');
        self::assertEquals('bundle1UserId', $data1->getName(), 'Data1 should not have changed');
        self::assertEquals('userId', $data2->getName(), 'Data2 should not have changed');

        // The second conflict should point back to data 2
        $conflictingData = $nameHelper->getData($conflictingName2);
        self::assertEquals($data2, $conflictingData);
        $conflictingBundle = $nameHelper->getBundleName($conflictingName);
        self::assertEquals('bundle1', $conflictingBundle);

        // Trying to resolve the naming conflict on the original element should now throw an exception
        // Because it cannot use 'id', 'userId' nor 'bundle1UserId'
        $nameHelper->assignDataName('bundle1', $conflictingData);
    }
}
