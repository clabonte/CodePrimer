# User Logout Business Process
This process is triggered when a user is leaving the application. Upon success, the context is wiped out to prevent its reuse.

## Overview
 - **Category**: Users
 - **Type**: Logout
 - **Trigger**: [Logout Request Event](#logoutrequestevent)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| *Anyone* | :white_check_mark: | :white_check_mark: | :x: | :x:

## Logout Request Event
Event triggered when user wants to leave the application
### Data
*N/A*

## Required Data
In order to handle the event above, this process also needs the following data:
### Context Data
Information from the context about the user to logout

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../DataModel/Overview.md#user) | id | Basic |



## Produced Data
N/A - *This process does not produce/update any data*

## Messages
### Message user.logout
user.logout: Message published when a user has successfully logged out from our application

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [User](../DataModel/Overview.md#user) | id | uuid | User&#039;s unique ID in our system | Basic |
| [User](../DataModel/Overview.md#user) | firstName | string | User first name | Basic |
| [User](../DataModel/Overview.md#user) | lastName | string | User last name | Basic |
| [User](../DataModel/Overview.md#user) | nickname | string | The name used to identify this user publicly in the application | Basic |
| [User](../DataModel/Overview.md#user) | email | email | User email address | Basic |

**Example**:
