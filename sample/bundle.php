<?php

use CodePrimer\Model\BusinessBundle;
require 'DatasetFactory.php';
require 'BusinessModelFactory.php';
require 'BusinessProcessFactory.php';

function prepareBundle()
{
    // Step 1 - Create your BusinessBundle
    $bundle = new BusinessBundle('io.codeprimer.sample', 'Channel');
    $bundle->setDescription('This sample application is used to show how model a business application using CodePrimer');

    // Step 2 - Add your Datasets to your Bundle
    $datasetFactory = new DatasetFactory();

    $bundle->addDataset($datasetFactory->createUserRole());
    $bundle->addDataset($datasetFactory->createUserStatus());
    $bundle->addDataset($datasetFactory->createArticleStatus());

    // Step 3 - Add your Business Data Model to your Bundle
    $modelFactory = new BusinessModelFactory($bundle);

    $bundle->addBusinessModel($modelFactory->createUserDataModel());
    $bundle->addBusinessModel($modelFactory->createArticleDataModel());
    $bundle->addBusinessModel($modelFactory->createArticleViewDataModel());
    $bundle->addBusinessModel($modelFactory->createTopicDataModel());
    $bundle->addBusinessModel($modelFactory->createLabelDataModel());
    $bundle->addBusinessModel($modelFactory->createSuggestedLabelDataModel());
    $bundle->addBusinessModel($modelFactory->createAccountDataModel());
    $bundle->addBusinessModel($modelFactory->createInterestDataModel());
    $bundle->addBusinessModel($modelFactory->createTransactionDataModel());
    $bundle->addBusinessModel($modelFactory->createPayoutDataModel());


    // Step 4 - Add your Business Process Model to your Bundle
    $processFactory = new BusinessProcessFactory($bundle);

    $bundle->addBusinessProcess($processFactory->createLoginProcess());
    $bundle->addBusinessProcess($processFactory->createLogoutProcess());
    $bundle->addBusinessProcess($processFactory->createRegisterProcess());

    $bundle->addBusinessProcess($processFactory->createNewArticleProcess());
    $bundle->addBusinessProcess($processFactory->createUpdateArticleProcess());
    $bundle->addBusinessProcess($processFactory->createSubmitArticleProcess());

    return $bundle;
}
