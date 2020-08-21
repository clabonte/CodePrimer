<?php

use CodePrimer\Helper\BusinessModelHelper;
use CodePrimer\Helper\FieldType;
use CodePrimer\Model\BusinessModel;
use CodePrimer\Model\Constraint;
use CodePrimer\Model\Field;

/**
 * Factory used to create the various BusinessModel instances needed in our Channel sample application.
 */
class BusinessModelFactory
{
    /** Creates the 'User' BusinessModel for our sample application.
     *  @see https://github.com/clabonte/codeprimer/blob/sample-app/doc/sample/DataModel.md
     */
    public function createUserDataModel()
    {
        $businessModelHelper = new BusinessModelHelper();

        $businessModel = new BusinessModel('User', 'A registered used in our application');
        $businessModel->setAudited(true);

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
            // TODO Replace by a DataSet once this feature is available
            ->addField(
                (new Field('role', FieldType::STRING, 'User role in the application'))
                    ->setExample('member')
                    ->setMandatory(true)
                    ->setDefault('member')
                    ->setSearchable(true)
                    ->setManaged(true)
            )
            // TODO Replace by a DataSet once this feature is available
            ->addField(
                (new Field('status', FieldType::STRING, 'User status'))
                    ->setExample('active')
                    ->setMandatory(true)
                    ->setDefault('active')
                    ->setSearchable(true)
                    ->setManaged(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', "User's account to track earnings"))
            ->addField(
                (new Field('articles', 'Article', 'List of articles owned by this user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('views', 'ArticleView', 'List of articles viewed by this user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('interests', 'Interest', 'List of topics user is interested in'))
                    ->setList(true)
            );

        // Step 3: Add internal fields
        $businessModelHelper->generateIdentifierField($businessModel);
        $businessModelHelper->generateTimestampFields($businessModel);

        // Step 4: Add unique field constraints along with the error message to use when violated
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

    public function createArticleDataModel()
    {
        $businessModel = new BusinessModel('Article', 'An article in our application');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('title', FieldType::STRING, 'Article title'))
                    ->setExample('How to go from idea to production-ready solution in a day with CodePrimer')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('description', FieldType::TEXT, 'Article description'))
                    ->setExample('This article explains how architects can save days/weeks of prepare to get a production-grade application up and running using the technology of their choice.')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('body', FieldType::TEXT, 'The article main body'))
                    ->setSearchable(true)
            )
            // TODO Replace by a DataSet once this feature is available
            ->addField(
                (new Field('status', FieldType::STRING, 'The article status'))
                    ->setExample('draft')
                    ->setMandatory(true)
                    ->setDefault('draft')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('author', 'User', 'User who created the article'))
            ->addField(new Field('topic', 'Topic', 'Topic to which this article belongs'))
            ->addField(
                (new Field('labels', 'Label', 'List of labels associated with this article by the author'))
                    ->setList(true)
            )
            ->addField(
                (new Field('views', 'ArticleView', 'List of views associated with this article'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('id', FieldType::UUID, "Article's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('22d5a494-ad3d-4032-9fbe-8f5eb0587396')
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this article was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this article was updated'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    public function createArticleViewDataModel()
    {
        $businessModel = new BusinessModel('ArticleView', 'An article view action by a registered user');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('count', FieldType::INTEGER, 'Number of times this user viewed this article', false, 1))

            // Step 2: Add business relations
            ->addField(new Field('user', 'User', 'User who viewed the article', true))
            ->addField(new Field('article', 'Article', 'Article associated with the view', true))

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this article was viewed the first time by this user'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this article was viewed the last time by this user'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    public function createTopicDataModel()
    {
        $businessModel = new BusinessModel('Topic', 'A high level topic that can be used to categorize articles');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('name', FieldType::STRING, 'A topic short description'))
                    ->setMandatory(true)
                    ->setExample('Technology')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('description', FieldType::STRING, 'A description of what kind of articles should be associated with'))
                    ->setMandatory(true)
                    ->setExample('Articles related to the latest trends in Technology to keep you up to date')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(
                (new Field('articles', 'Article', 'List of articles associated with this topic'))
                    ->setList(true)
            )
            ->addField(
                (new Field('suggested labels', 'SuggestedLabel', 'List of labels that are often associated with this topic'))
                    ->setList(true)
            );

        // Step 4: Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueName'))
                    ->addField($businessModel->getField('name'))
                    ->setDescription('The topic name must be unique.')
                    ->setErrorMessage('This topic name is already in use. Please select another one.')
            );

        return $businessModel;
    }

    public function createLabelDataModel()
    {
        $businessModel = new BusinessModel('Label', 'A tag that can be associated with an article by an author to help in its classification');

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('tag', FieldType::STRING, 'A unique tag'))
                    ->setMandatory(true)
                    ->setExample('PHP')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(
                (new Field('articles', 'Article', 'List of articles associated with this tag'))
                    ->setList(true)
            );

        // Step 4: Add unique field constraints along with the error message to use when violated
        $businessModel
            ->addUniqueConstraint(
                (new Constraint('uniqueTag'))
                    ->addField($businessModel->getField('tag'))
                    ->setDescription('The tag must be unique.')
                    ->setErrorMessage('This tag is already in use. Please select another one.')
            );

        return $businessModel;
    }

    public function createSuggestedLabelDataModel()
    {
        $businessModel = new BusinessModel('SuggestedLabel', 'Labels often associated with a given topic');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('count', FieldType::INTEGER, 'Number of times this label has been associated with this topic', false, 1))

            // Step 2: Add business relations
            ->addField(new Field('label', 'Label', 'Label associated with this suggestion', true))
            ->addField(new Field('topic', 'Topic', 'Topic associated with this suggestion', true));

        return $businessModel;
    }

    public function createAccountDataModel()
    {
        $businessModel = new BusinessModel('Account', 'Author account to track earnings');
        $businessModel->setAudited(true);

        // Step 1: Add business attributes
        $businessModel
            ->addField(
                (new Field('balance', FieldType::PRICE, 'Current amount owed to the author'))
                    ->setExample('$9.90')
                    ->setSearchable(true)
            )
            ->addField(
                (new Field('lifetime', FieldType::PRICE, 'Lifetime earnings associated with this account'))
                    ->setExample('$200')
                    ->setSearchable(true)
            )

            // Step 2: Add business relations
            ->addField(new Field('member', 'User', 'Member associated with this account', true))
            ->addField(new Field('topic', 'Topic', 'Topic to which this article belongs'))
            ->addField(
                (new Field('payouts', 'Payout', 'List of payouts already made to the user'))
                    ->setList(true)
            )
            ->addField(
                (new Field('transactions', 'Transaction', 'List of transactions used to track earnings details'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('id', FieldType::UUID, "Account's unique ID in our system"))
                    ->setMandatory(true)
                    ->setManaged(true)
                    ->setExample('b34d38eb-1164-4289-98b4-65706837c4d7')
            )
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this account was created'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this account was updated last'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    public function createInterestDataModel()
    {
        $businessModel = new BusinessModel('Interest', 'Interest expressed by a user to be notified of new articles');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('instantNotification', FieldType::BOOLEAN, 'Whether the user wants to be notified ASAP when a new article matching this interest is published', false, false))

            // Step 2: Add business relations
            ->addField(new Field('member', 'User', 'User who expressed the interest', true))
            ->addField(new Field('label', 'Label', 'Label associated with this interest'))
            ->addField(new Field('topic', 'Topic', 'Topic associated with this interest', true));

        return $businessModel;
    }

    public function createTransactionDataModel()
    {
        $businessModel = new BusinessModel('Transaction', 'An article view that is tied with some earnings');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('amount', FieldType::PRICE, 'Earnings associated with this transaction', true))

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', 'Account associated with this transaction', true))
            ->addField(new Field('articleView', 'ArticleView', 'ArticleView that triggered the transaction', true))
            ->addField(new Field('payout', 'Payout', 'The payout associated with this transaction, set once the payout is issued'))

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this transaction was viewed the first time by this user'))
                    ->setManaged(true)
            );

        return $businessModel;
    }

    public function createPayoutDataModel()
    {
        $businessModel = new BusinessModel('Payout', 'Tracks payment made to an author');

        // Step 1: Add business attributes
        $businessModel
            ->addField(new Field('amount', FieldType::PRICE, 'Amount associated with this payout', true))

            // Step 2: Add business relations
            ->addField(new Field('account', 'Account', 'Account associated with this transaction', true))
            ->addField(
                (new Field('transactions', 'Transaction', 'The list of transactions associated with this payout'))
                    ->setList(true)
            )

            // Step 3: Add internal fields
            ->addField(
                (new Field('created', FieldType::DATETIME, 'The date and time at which this payment was issued'))
                    ->setManaged(true)
            )
            ->addField(
                (new Field('updated', FieldType::DATETIME, 'The date and time at which this payment was updated last'))
                    ->setManaged(true)
            );

        return $businessModel;
    }
}
