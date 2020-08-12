# User Login Business Process
This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information.

## Overview
 - **Category**: Users
 - **Type**: Login
 - **Trigger**: [Login Request Event](#loginrequestevent)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| *Anyone* | :white_check_mark: | :white_check_mark: | :x: | :x:

## Login Request Event
Event triggered when user wants to login with the application
### Data
    
| BusinessModel | Field | Mandatory | Level |
| ------------- | ----- | --------- | ----- |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | email | yes | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | password | yes | Basic |

## Required Data
N/A - *This process does not require any other data to process the event*

## Produced Data
Upon successful completion, this process will produce/update the following data:
### Context Data
User information to add to the context

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | id | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | firstName | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | lastName | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | nickname | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | email | Basic |



## Messages
### Message user.login
user.login: Message published when a user has successfully authenticated with our application

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [User](../../../../../doc/sample/docs/DataModel/User.md) | id | uuid | User&#039;s unique ID in our system | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | firstName | string | User first name | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | lastName | string | User last name | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | nickname | string | The name used to identify this user publicly in the application | Basic |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | email | email | User email address | Basic |

**Example**:
