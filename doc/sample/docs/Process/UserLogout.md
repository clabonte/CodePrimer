# User Logout Business Process
This process is triggered when a user is leaving the application. Upon success, the context is wiped out to prevent its reuse.

## Overview
 - **Category**: Users
 - **Type**: Logout
 - **Trigger**: [Logout Request Event](#logout-request-event)

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

**Type**: Structure

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [`User`](../DataModel/Overview.md#user) | id | uuid | *N/A* |



## Produced Data
N/A - *This process does not produce/update any data*

## Returned Data
N/A - *This process does not produce/update any data*

## Messages
### Message user.logout
user.logout: Message published when a user has successfully logged out from our application

**Data**:

| Variable | Type | BusinessModel | Field | Description | Level |
| -------- | ---- | ------------- | ----- | ----------- | ------|
| id | uuid | [`User`](../DataModel/Overview.md#user) | id | Business model unique identifier field | *N/A* |
| firstName | string | [`User`](../DataModel/Overview.md#user) | firstName | User first name | *N/A* |
| lastName | string | [`User`](../DataModel/Overview.md#user) | lastName | User last name | *N/A* |
| nickname | string | [`User`](../DataModel/Overview.md#user) | nickname | The name used to identify this user publicly in the application | *N/A* |
| email | email | [`User`](../DataModel/Overview.md#user) | email | User email address | *N/A* |

**Example**:

---
[Back to list](Overview.md)