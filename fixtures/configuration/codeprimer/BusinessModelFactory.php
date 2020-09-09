<?php

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessBundle;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Field;

/**
 * This factory is used to create the various BusinessModel instances that are part of your application.
 * Instructions:
 *  - Create a set of public methods starting with the 'create' name prefix (e.g. createUserModel())
 *  - Each 'create' method must return a 'BusinessModel' instance describing a business model used in your application.
 *  - The bundle.php file will automatically invoke all the 'create' methods to add all BusinessModel instances to your application business bundle.
 */
class BusinessModelFactory
{
    /** @var BusinessBundle */
    private $businessBundle;

    /** @var BusinessModelHelper */
    private $businessModelHelper;

    /**
     * BusinessModelFactory constructor.
     */
    public function __construct(BusinessBundle $businessBundle)
    {
        $this->businessBundle = $businessBundle;
        $this->businessModelHelper = new BusinessModelHelper();
    }

    /**
     * This is a sample method to illustrate how to create a simple BusinessModel object to use by CodePrimer
     * TODO Delete or update this method based on your needs.
     */
    public function createUserModel(): BusinessModel
    {
        $businessModel = new BusinessModel('User', 'A registered used in our application');

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('firstName', FieldType::STRING, 'User first name'))
                    ->setExample('John')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('lastName', FieldType::STRING, 'User last name'))
                    ->setExample('Doe')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('nickname', FieldType::STRING, 'The name used to identify this user publicly in the application'))
                    ->setExample('JohnDoe')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('email', FieldType::EMAIL, 'User email address'))
                    ->setExample('john.doe@test.com')
                    ->setMandatory(true)
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('password', FieldType::PASSWORD, 'User password to access our application'))
                    ->setMandatory(true)
            )
            ->addField(
                (new Field('role', 'UserRole', 'User role in the application'))
                    ->setExample('member', $this->businessBundle)
                    ->setMandatory(true)
                    ->setDefault('member', $this->businessBundle)
                    ->setSearchable(true)
                    ->setManaged(true)
            )
            ->addField(
                (new Field('status', 'UserStatus', 'User status'))
                    ->setExample('active', $this->businessBundle)
                    ->setMandatory(true)
                    ->setDefault('active', $this->businessBundle)
                    ->setSearchable(true)
                    ->setManaged(true)
            );

        // Step 2 (Optional): Add business relations

        // Step 3 (Optional): Add internal fields
        $this->businessModelHelper->generateIdentifierField($businessModel);
        $this->businessModelHelper->generateTimestampFields($businessModel);

        // Step 4 (Optional): Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueEmail'))
                    ->addField($businessModel->getField('email'))
                    ->setDescription('The email address must uniquely identify the user for login in')
                    ->setErrorMessage('This email address is already in use. Please select another one or recover your password if you forgot it.')
            )
            ->addUniqueConstraint(
                (new Constraint('uniqueNickname'))
                    ->addField($businessModel->getField('nickname'))
                    ->setDescription("The nickname uniquely identifies the user in the application's public spaces")
                    ->setErrorMessage('This nickname name is already in use. Please select another one.')
            );

        return $businessModel;
    }

    // TODO Add other create methods for each BusinessModel used by your application
}
