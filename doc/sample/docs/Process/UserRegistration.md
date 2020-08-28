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

**Type**: Structure

| BusinessModel | Field | Type | Mandatory | Level |
| ------------- | ----- | ---- | --------- | ----- |
| [`User`](../DataModel/Overview.md#user) | email | email | yes | *N/A* |
| [`User`](../DataModel/Overview.md#user) | password | password | yes | *N/A* |
| [`User`](../DataModel/Overview.md#user) | firstName | string | no | *N/A* |
| [`User`](../DataModel/Overview.md#user) | lastName | string | no | *N/A* |
| [`User`](../DataModel/Overview.md#user) | nickname | string | no | *N/A* |

## Required Data
N/A - *This process does not require any other data to process the event*

## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
User profile created

**Type**: Structure

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [`User`](../DataModel/Overview.md#user) | firstName | string | *N/A* |
| [`User`](../DataModel/Overview.md#user) | lastName | string | *N/A* |
| [`User`](../DataModel/Overview.md#user) | nickname | string | *N/A* |
| [`User`](../DataModel/Overview.md#user) | email | email | *N/A* |
| [`User`](../DataModel/Overview.md#user) | password | password | *N/A* |
| [`User`](../DataModel/Overview.md#user) | role | [`UserRole`](../Dataset/Overview.md#userrole) | Reference |
| [`User`](../DataModel/Overview.md#user) | status | [`UserStatus`](../Dataset/Overview.md#userstatus) | Reference |
| [`User`](../DataModel/Overview.md#user) | id | uuid | *N/A* |
| [`User`](../DataModel/Overview.md#user) | created | datetime | *N/A* |
| [`User`](../DataModel/Overview.md#user) | updated | datetime | *N/A* |


## Returned Data
N/A - *This process does not produce/update any data*

## Messages
### Message user.new
user.new: Message published when a new user has been created in our application

**Data**:

| Variable | Type | BusinessModel | Field | Description | Level |
| -------- | ---- | ------------- | ----- | ----------- | ------|
| firstName | string | [`User`](../DataModel/Overview.md#user) | firstName | User first name | *N/A* |
| lastName | string | [`User`](../DataModel/Overview.md#user) | lastName | User last name | *N/A* |
| nickname | string | [`User`](../DataModel/Overview.md#user) | nickname | The name used to identify this user publicly in the application | *N/A* |
| email | email | [`User`](../DataModel/Overview.md#user) | email | User email address | *N/A* |
| role | [`UserRole`](../Dataset/Overview.md#userrole) | [`User`](../DataModel/Overview.md#user) | role | User role in the application | Reference |
| status | [`UserStatus`](../Dataset/Overview.md#userstatus) | [`User`](../DataModel/Overview.md#user) | status | User status | Reference |
| id | uuid | [`User`](../DataModel/Overview.md#user) | id | Business model unique identifier field | *N/A* |
| created | datetime | [`User`](../DataModel/Overview.md#user) | created | The date and time at which this User was created | *N/A* |
| updated | datetime | [`User`](../DataModel/Overview.md#user) | updated | The date and time at which this User was last updated | *N/A* |

**Example**:

---
[Back to list](Overview.md)