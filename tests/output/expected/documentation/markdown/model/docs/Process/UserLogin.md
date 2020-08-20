# User Login Business Process
This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information.

## Overview
 - **Category**: Users
 - **Type**: Login
 - **Trigger**: [Login Request Event](#login-request-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| *Anyone* | :white_check_mark: | :white_check_mark: | :x: | :x:

## Login Request Event
Event triggered when user wants to login with the application
### Data
**Type**: Structure

| BusinessModel | Field | Type | Mandatory | Level |
| ------------- | ----- | ---- | --------- | ----- |
| [User](../DataModel/Overview.md#user) | email | email | yes | *N/A* |
| [User](../DataModel/Overview.md#user) | password | password | yes | *N/A* |

## Required Data
N/A - *This process does not require any other data to process the event*

## Produced Data
Upon successful completion, this process will produce/update the following data:
### Context Data
User information to add to the context
**Type**: Structure

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [User](../DataModel/Overview.md#user) | id | uuid | *N/A* |
| [User](../DataModel/Overview.md#user) | firstName | string | *N/A* |
| [User](../DataModel/Overview.md#user) | lastName | string | *N/A* |
| [User](../DataModel/Overview.md#user) | nickname | string | *N/A* |
| [User](../DataModel/Overview.md#user) | email | email | *N/A* |



## Returned Data
N/A - *This process does not produce/update any data*

## Messages
### Message user.login
user.login: Message published when a user has successfully authenticated with our application

**Data**:

| Variable | Type | BusinessModel | Field | Description | Level |
| -------- | ---- | ------------- | ----- | ----------- | ------|
| id | uuid | [User](../DataModel/Overview.md#user) | id | The user&#039;s unique ID in our system | *N/A* |
| firstName | string | [User](../DataModel/Overview.md#user) | firstName | User first name | *N/A* |
| lastName | string | [User](../DataModel/Overview.md#user) | lastName | User last name | *N/A* |
| nickname | string | [User](../DataModel/Overview.md#user) | nickname | The name used to identify this user publicly on the site | *N/A* |
| email | email | [User](../DataModel/Overview.md#user) | email | User email address | *N/A* |

**Example**:

---
[Back to list](Overview.md)