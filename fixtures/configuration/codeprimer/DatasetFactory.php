<?php

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;

/**
 * This factory is used to create the various Dataset instances that are part of your application.
 * Instructions:
 *  - Create a set of public methods starting with the 'create' name prefix (e.g. createUserRole())
 *  - Each 'create' method must return a 'Dataset' instance describing a dataset used in your application.
 *  - The bundle.php file will automatically invoke all the 'create' methods to add all Dataset instances to your application business bundle.
 */
class DatasetFactory
{
    /**
     * This is a sample method to illustrate the creation of a Dataset object to use by CodePrimer (see the User BusinessModel)
     * TODO Delete or update this method based on your needs.
     */
    public function createUserRole(): Dataset
    {
        $dataset = new Dataset('UserRole', 'Roles that can be assigned to our application users');
        $dataset->setFields([
            (new Field('name', FieldType::STRING, 'The role name, as used in our authorization scheme'))->setIdentifier(true),
            new Field('description', FieldType::STRING, 'A high-level description of what this role is used for'),
            new Field('fullAccess', FieldType::BOOLEAN, 'Whether this role provides full access to the solution'),
        ]);

        $dataset->setElements([
            new DatasetElement([
                'name' => 'admin',
                'description' => 'Role reserved to internal employees with full access',
                'fullAccess' => true,
            ]),
            new DatasetElement([
                'name' => 'author',
                'description' => 'Role reserved to registered users who can submit articles in our application',
                'fullAccess' => false,
            ]),
            new DatasetElement([
                'name' => 'premium',
                'description' => 'Role reserved to registered users with a paying subscription in our application',
                'fullAccess' => false,
            ]),
            new DatasetElement([
                'name' => 'member',
                'description' => 'Role reserved to registered users under the free plan',
                'fullAccess' => false,
            ]),
        ]);

        return $dataset;
    }

    /**
     * This is another sample method to illustrate the creation of a Dataset object to use by CodePrimer (see the User BusinessModel)
     * TODO Delete or update this method based on your needs.
     */
    public function createUserStatus(): Dataset
    {
        $dataset = new Dataset('UserStatus', 'Status that can be assigned to a user');
        $dataset->setFields([
            (new Field('name', FieldType::STRING, 'The user status'))->setIdentifier(true),
            new Field('description', FieldType::STRING, 'A high-level description of what this status represents'),
            new Field('accessAllowed', FieldType::BOOLEAN, 'Whether the user can access the application with this status'),
        ]);

        $dataset->setElements([
            new DatasetElement([
                'name' => 'active',
                'description' => 'User is active and allowed to access the application.',
                'accessAllowed' => true,
            ]),
            new DatasetElement([
                'name' => 'pending',
                'description' => 'User registration has started but not completed yet. He is not allowed to access the application until registration is complete',
                'accessAllowed' => false,
            ]),
            new DatasetElement([
                'name' => 'locked',
                'description' => 'User is locked out after too many false attempts. User must either reset his password or wait until the account is automatically unlocked after a pre-configured delay.',
                'accessAllowed' => false,
            ]),
            new DatasetElement([
                'name' => 'canceled',
                'description' => 'User account has been canceled at the user request',
                'accessAllowed' => false,
            ]),
        ]);

        return $dataset;
    }

    // TODO Add other create methods for each Dataset used by your application
}
