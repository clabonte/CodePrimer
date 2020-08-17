# User Registration Business Process
This process is triggered when a user wants to register with our application. Upon success, the user is created internally but is not logged in yet.

## Overview
 - **Category**: Users
 - **Type**: Register
 - **Trigger**: [Registration Request Event](#registration-request-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| *Anyone* | :white_check_mark: | :white_check_mark: | :x: | :x:

## Registration Request Event
Event triggered when user wants to register with the application
### Data
    
| BusinessModel | Field | Type | Mandatory | Level |
| ------------- | ----- | ---- | --------- | ----- |
| [User](../DataModel/Overview.md#user) | email | email | yes | *N/A* |
| [User](../DataModel/Overview.md#user) | password | password | yes | *N/A* |
| [User](../DataModel/Overview.md#user) | firstName | string | no | *N/A* |
| [User](../DataModel/Overview.md#user) | lastName | string | no | *N/A* |
| [User](../DataModel/Overview.md#user) | nickname | string | no | *N/A* |

## Required Data
N/A - *This process does not require any other data to process the event*

## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
User profile created

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [User](../DataModel/Overview.md#user) | id | uuid | *N/A* |
| [User](../DataModel/Overview.md#user) | firstName | string | *N/A* |
| [User](../DataModel/Overview.md#user) | lastName | string | *N/A* |
| [User](../DataModel/Overview.md#user) | nickname | string | *N/A* |
| [User](../DataModel/Overview.md#user) | email | email | *N/A* |
| [User](../DataModel/Overview.md#user) | password | password | *N/A* |
| [User](../DataModel/Overview.md#user) | created | datetime | *N/A* |
| [User](../DataModel/Overview.md#user) | updated | datetime | *N/A* |
| [User](../DataModel/Overview.md#user) | crmId | string | *N/A* |
| [User](../DataModel/Overview.md#user) | activationCode | randomstring | *N/A* |


## Returned Data
N/A - *This process does not produce/update any data*

## Messages
### Message user.new
user.new: Message published when a new user has been created in our application

**Data**:

| Variable | Type | BusinessModel | Field | Description | Level |
| -------- | ---- | ------------- | ----- | ----------- | ------|
| id | uuid | [User](../DataModel/Overview.md#user) | id | The user&#039;s unique ID in our system | *N/A* |
| firstName | string | [User](../DataModel/Overview.md#user) | firstName | User first name | *N/A* |
| lastName | string | [User](../DataModel/Overview.md#user) | lastName | User last name | *N/A* |
| nickname | string | [User](../DataModel/Overview.md#user) | nickname | The name used to identify this user publicly on the site | *N/A* |
| email | email | [User](../DataModel/Overview.md#user) | email | User email address | *N/A* |
| created | datetime | [User](../DataModel/Overview.md#user) | created | The date and time at which this user was created | *N/A* |
| updated | datetime | [User](../DataModel/Overview.md#user) | updated | The date and time at which this user was updated | *N/A* |
| crmId | string | [User](../DataModel/Overview.md#user) | crmId | The ID of this user in our external CRM | *N/A* |
| activationCode | randomstring | [User](../DataModel/Overview.md#user) | activationCode | The code required to validate the user&#039;s account | *N/A* |

**Example**:

---
[Back to list](Overview.md)