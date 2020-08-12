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
    
| BusinessModel | Field | Mandatory | Level |
| ------------- | ----- | --------- | ----- |
| [User](../DataModel/Overview.md#user) | email | yes | Basic |
| [User](../DataModel/Overview.md#user) | password | yes | Basic |
| [User](../DataModel/Overview.md#user) | firstName | no | Basic |
| [User](../DataModel/Overview.md#user) | lastName | no | Basic |
| [User](../DataModel/Overview.md#user) | nickname | no | Basic |

## Required Data
N/A - *This process does not require any other data to process the event*

## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
User profile created

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../DataModel/Overview.md#user) | firstName | Basic |
| [User](../DataModel/Overview.md#user) | lastName | Basic |
| [User](../DataModel/Overview.md#user) | nickname | Basic |
| [User](../DataModel/Overview.md#user) | email | Basic |
| [User](../DataModel/Overview.md#user) | password | Basic |
| [User](../DataModel/Overview.md#user) | role | Basic |
| [User](../DataModel/Overview.md#user) | status | Basic |
| [User](../DataModel/Overview.md#user) | id | Basic |
| [User](../DataModel/Overview.md#user) | created | Basic |
| [User](../DataModel/Overview.md#user) | updated | Basic |


## Messages
### Message user.new
user.new: Message published when a new user has been created in our application

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [User](../DataModel/Overview.md#user) | firstName | string | User first name | Basic |
| [User](../DataModel/Overview.md#user) | lastName | string | User last name | Basic |
| [User](../DataModel/Overview.md#user) | nickname | string | The name used to identify this user publicly in the application | Basic |
| [User](../DataModel/Overview.md#user) | email | email | User email address | Basic |
| [User](../DataModel/Overview.md#user) | role | string | User role in the application | Basic |
| [User](../DataModel/Overview.md#user) | status | string | User status | Basic |
| [User](../DataModel/Overview.md#user) | id | uuid | User&#039;s unique ID in our system | Basic |
| [User](../DataModel/Overview.md#user) | created | datetime | The date and time at which this user was created | Basic |
| [User](../DataModel/Overview.md#user) | updated | datetime | The date and time at which this user was updated | Basic |

**Example**:

---
[Back to list](Overview.md)