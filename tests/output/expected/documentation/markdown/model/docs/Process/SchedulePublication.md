# Schedule Publication Business Process
This process is triggered when a user wants to publish a specific post at at given time.

## Overview
 - **Category**: Posts
 - **Type**: Update
 - **Trigger**: [Schedule Post Event](#schedule-post-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| *Anyone* | :white_check_mark: | :white_check_mark: | :x: | :x:

## Schedule Post Event
Event triggered when user wants to schedule a post at a given time
### Data
**Type**: Structure

| BusinessModel | Field | Type | Mandatory | Level |
| ------------- | ----- | ---- | --------- | ----- |
| [Post](../DataModel/Overview.md#post) | id | uuid | yes | *N/A* |
| [Post](../DataModel/Overview.md#post) | scheduled | datetime | yes | *N/A* |

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
N/A - *This process does not produce any message*

---
[Back to list](Overview.md)