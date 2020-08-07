<?php

namespace CodePrimer\Tests\Model;

use CodePrimer\Model\DataBundle;
use CodePrimer\Model\Package;
use CodePrimer\Tests\Helper\TestHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataBundleTest extends TestCase
{
    /** @var DataBundle */
    private $dataBundle;

    /** @var Package */
    private $businessBundle;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataBundle = new DataBundle(DataBundle::INPUT, 'TestBundle', 'Test Source', 'Test Description');
        $this->businessBundle = TestHelper::getSamplePackage();
    }

    public function testDefaultValues()
    {
        self::assertEquals(DataBundle::INPUT, $this->dataBundle->getOrigin());
        self::assertEquals('Test Source', $this->dataBundle->getSource());
        self::assertEquals('TestBundle', $this->dataBundle->getName());
        self::assertEquals('TestBundle', $this->dataBundle->getName());
        self::assertEquals('Test Description', $this->dataBundle->getDescription());
        self::assertEmpty($this->dataBundle->getBusinessModels());
        self::assertEmpty($this->dataBundle->getMandatoryFields());
        self::assertEmpty($this->dataBundle->getOptionalFields());

        self::assertFalse($this->dataBundle->isBusinessModelPresent('User'));
        self::assertEmpty($this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertEmpty($this->dataBundle->getBusinessModelOptionalFields('User'));
    }

    public function testAddBusinessModelShouldAddAllFieldsInBasedOnModelDefinition()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $post = $this->businessBundle->getBusinessModel('Post');
        $this->dataBundle->addBusinessModel($user);

        // Adding a first data model works as expected
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(12, $this->dataBundle->getBusinessModelOptionalFields('User'));
        self::assertFalse($this->dataBundle->isBusinessModelPresent('Post'));

        // Adding the same model again does not change the result
        $this->dataBundle->addBusinessModel($user);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(12, $this->dataBundle->getBusinessModelOptionalFields('User'));
        self::assertFalse($this->dataBundle->isBusinessModelPresent('Post'));

        // Adding a second model works as expected
        $this->dataBundle->addBusinessModel($post);
        self::assertCount(2, $this->dataBundle->getBusinessModels());
        self::assertCount(2, $this->dataBundle->getMandatoryFields());
        self::assertCount(2, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(12, $this->dataBundle->getBusinessModelOptionalFields('User'));

        self::assertTrue($this->dataBundle->isBusinessModelPresent('Post'));
        self::assertCount(4, $this->dataBundle->getBusinessModelMandatoryFields('Post'));
        self::assertCount(2, $this->dataBundle->getBusinessModelOptionalFields('Post'));
    }

    public function testAddBusinessModelFieldsShouldOnlyAddUnmanagedFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');
        $post = $this->businessBundle->getBusinessModel('Post');
        $this->dataBundle->addFields($user);

        // Adding a first data model works as expected
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(2, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(9, $this->dataBundle->getBusinessModelOptionalFields('User'));
        self::assertFalse($this->dataBundle->isBusinessModelPresent('Post'));

        // Adding the same model again does not change the result
        $this->dataBundle->addFields($user);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(2, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(9, $this->dataBundle->getBusinessModelOptionalFields('User'));
        self::assertFalse($this->dataBundle->isBusinessModelPresent('Post'));

        // Adding a second model works as expected
        $this->dataBundle->addFields($post);
        self::assertCount(2, $this->dataBundle->getBusinessModels());
        self::assertCount(2, $this->dataBundle->getMandatoryFields());
        self::assertCount(2, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(2, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(9, $this->dataBundle->getBusinessModelOptionalFields('User'));

        self::assertTrue($this->dataBundle->isBusinessModelPresent('Post'));
        self::assertCount(4, $this->dataBundle->getBusinessModelMandatoryFields('Post'));
        self::assertCount(0, $this->dataBundle->getBusinessModelOptionalFields('Post'));
    }

    public function testAddSameBusinessModelProperlyOverridesFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        // Adding a partial data model works as expected
        $this->dataBundle->addFields($user);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(2, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(9, $this->dataBundle->getBusinessModelOptionalFields('User'));

        // Adding the full model overrides data stored
        $this->dataBundle->addBusinessModel($user);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(12, $this->dataBundle->getBusinessModelOptionalFields('User'));

        // Adding a partial data model back overrides data stored
        $this->dataBundle->addFields($user);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(2, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(9, $this->dataBundle->getBusinessModelOptionalFields('User'));
    }

    public function testAddOnlySpecificFieldNamesShouldNotAddOtherFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        // Adding only mandatory fields
        $this->dataBundle->addFields($user, ['firstName', 'lastName', 'email']);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(0, $this->dataBundle->getBusinessModelOptionalFields('User'));
        $fields = $this->dataBundle->getBusinessModelMandatoryFields('User');
        self::assertArrayHasKey('firstName', $fields);
        self::assertArrayHasKey('lastName', $fields);
        self::assertArrayHasKey('email', $fields);

        // Adding only optional fields this time
        $this->dataBundle->addFields($user, [], ['firstName', 'lastName', 'email']);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(0, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelOptionalFields('User'));
        $fields = $this->dataBundle->getBusinessModelOptionalFields('User');
        self::assertArrayHasKey('firstName', $fields);
        self::assertArrayHasKey('lastName', $fields);
        self::assertArrayHasKey('email', $fields);
    }

    public function testAddOnlySpecificFieldObjectsShouldNotAddOtherFields()
    {
        $user = $this->businessBundle->getBusinessModel('User');

        // Adding only mandatory fields
        $this->dataBundle->addFields($user, [$user->getField('firstName'), $user->getField('lastName'), $user->getField('email')]);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(0, $this->dataBundle->getBusinessModelOptionalFields('User'));
        $fields = $this->dataBundle->getBusinessModelMandatoryFields('User');
        self::assertArrayHasKey('firstName', $fields);
        self::assertArrayHasKey('lastName', $fields);
        self::assertArrayHasKey('email', $fields);

        // Adding only optional fields this time
        $this->dataBundle->addFields($user, [], [$user->getField('firstName'), $user->getField('lastName'), $user->getField('email')]);
        self::assertCount(1, $this->dataBundle->getBusinessModels());
        self::assertCount(1, $this->dataBundle->getMandatoryFields());
        self::assertCount(1, $this->dataBundle->getOptionalFields());

        self::assertTrue($this->dataBundle->isBusinessModelPresent('User'));
        self::assertCount(0, $this->dataBundle->getBusinessModelMandatoryFields('User'));
        self::assertCount(3, $this->dataBundle->getBusinessModelOptionalFields('User'));
        $fields = $this->dataBundle->getBusinessModelOptionalFields('User');
        self::assertArrayHasKey('firstName', $fields);
        self::assertArrayHasKey('lastName', $fields);
        self::assertArrayHasKey('email', $fields);
    }

    public function testAddInvalidMandatoryFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested mandatory field unknown is not defined in BusinessModel User');

        $user = $this->businessBundle->getBusinessModel('User');
        $this->dataBundle->addFields($user, ['firstName', 'lastName', 'unknown']);
    }

    public function testAddInvalidOptionalFieldThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested optional field unknown is not defined in BusinessModel User');

        $user = $this->businessBundle->getBusinessModel('User');
        $this->dataBundle->addFields($user, ['firstName'], ['lastName', 'unknown']);
    }

    public function testAddSameFieldMandatoryAndOptionalThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Requested field firstName for BusinessModel User cannot be both mandatory and optional');

        $user = $this->businessBundle->getBusinessModel('User');
        $this->dataBundle->addFields($user, ['firstName'], ['firstName']);
    }

    public function testInvalidOriginThrowsException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid origin provided: invalid. Must be one of: input, context, internal or external');

        $bundle = new DataBundle('invalid');
    }
}
