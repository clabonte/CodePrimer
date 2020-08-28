<?php

use CodePrimer\Helper\FieldType;
use CodePrimer\Model\Dataset;
use CodePrimer\Model\DatasetElement;
use CodePrimer\Model\Field;

/**
 * Factory used to create the various Dataset instances needed in our Channel sample application.
 */
class DatasetFactory
{
    /**
     * Creates the 'UserRole' Dataset to use in our application.
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
     * Creates the 'UserStatus' Dataset to use in our application.
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

    /**
     * Creates the 'ArticleStatus' Dataset to use in our application.
     */
    public function createArticleStatus(): Dataset
    {
        $dataset = new Dataset('ArticleStatus', 'Status that can be assigned to an article');
        $dataset->setFields([
            (new Field('name', FieldType::STRING, 'The article status'))->setIdentifier(true),
            new Field('description', FieldType::STRING, 'A high-level description of what this status represents'),
        ]);

        $dataset->setElements([
            new DatasetElement([
                'name' => 'draft',
                'description' => 'Article is still being worked on by the author.',
            ]),
            new DatasetElement([
                'name' => 'review',
                'description' => 'Article has been submitted for approval by the author.',
            ]),
            new DatasetElement([
                'name' => 'pending',
                'description' => 'Article has been approved for publishing by an Admin on a given date and time.',
            ]),
            new DatasetElement([
                'name' => 'rejected',
                'description' => 'Article has been rejected with comments by an Admin and must be reworked by the Author.',
            ]),
            new DatasetElement([
                'name' => 'published',
                'description' => 'Article is published and visible in the application.',
            ]),
            new DatasetElement([
                'name' => 'removed',
                'description' => 'Article has been removed by the Author or an Admin.',
            ]),
        ]);

        return $dataset;
    }
}
