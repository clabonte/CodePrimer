# Submit Article for Review Business Process
This process is triggered when an author has finished editing an article and is ready to submit it for review.

## Overview
 - **Category**: Articles
 - **Type**: Status Update
 - **Trigger**: [Submit Article Event](#submit-article-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| Author | :white_check_mark: | :white_check_mark: | :x: | :x:

## Submit Article Event
Event triggered when an article is submitted for review
### Data
    
| BusinessModel | Field | Mandatory | Level |
| ------------- | ----- | --------- | ----- |
| [Article](../DataModel/Overview.md#article) | id | yes | Basic |

## Required Data
In order to handle the event above, this process also needs the following data:
### Context Data
Retrieve the user id from the context to ensure he is the article&#039;s author

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../DataModel/Overview.md#user) | id | Basic |



## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
Update the article status

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [Article](../DataModel/Overview.md#article) | status | Basic |


## Messages
N/A - *This process does not produce any message*

---
[Back to list](Overview.md)