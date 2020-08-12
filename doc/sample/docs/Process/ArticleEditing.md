# Article Editing Business Process
This process is triggered when an author wants to modify one of his existing articles.

## Overview
 - **Category**: Articles
 - **Type**: Update
 - **Trigger**: [Update Article Event](#update-article-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| Author | :white_check_mark: | :white_check_mark: | :x: | :x:

## Update Article Event
Event triggered when an existing article is updated by its author
### Data
    
| BusinessModel | Field | Mandatory | Level |
| ------------- | ----- | --------- | ----- |
| [Article](../DataModel/Overview.md#article) | topic | no | Reference |
| [Article](../DataModel/Overview.md#article) | title | no | Basic |
| [Article](../DataModel/Overview.md#article) | body | no | Basic |
| [Article](../DataModel/Overview.md#article) | description | no | Basic |
| [Article](../DataModel/Overview.md#article) | labels | no | Full |

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
Update the article internally

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [Article](../DataModel/Overview.md#article) | title | Basic |
| [Article](../DataModel/Overview.md#article) | description | Basic |
| [Article](../DataModel/Overview.md#article) | body | Basic |
| [Article](../DataModel/Overview.md#article) | status | Basic |
| [Article](../DataModel/Overview.md#article) | author | Reference |
| [Article](../DataModel/Overview.md#article) | topic | Reference |
| [Article](../DataModel/Overview.md#article) | labels | Reference |
| [Article](../DataModel/Overview.md#article) | views | Reference |
| [Article](../DataModel/Overview.md#article) | id | Basic |
| [Article](../DataModel/Overview.md#article) | created | Basic |
| [Article](../DataModel/Overview.md#article) | updated | Basic |


## Messages
### Message article.updated
article.updated: Message published when an existing article has been updated by a user

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [Article](../DataModel/Overview.md#article) | title | string | Article title | Basic |
| [Article](../DataModel/Overview.md#article) | description | text | Article description | Basic |
| [Article](../DataModel/Overview.md#article) | body | text | The article main body | Basic |
| [Article](../DataModel/Overview.md#article) | status | string | The article status | Basic |
| [Article](../DataModel/Overview.md#article) | author | [User](../DataModel/Overview.md#user) | User who created the article | Full |
| [Article](../DataModel/Overview.md#article) | topic | [Topic](../DataModel/Overview.md#topic) | Topic to which this article belongs | Full |
| [Article](../DataModel/Overview.md#article) | labels | List of [Label](../DataModel/Overview.md#label) | List of labels associated with this article by the author | Full |
| [Article](../DataModel/Overview.md#article) | id | uuid | Article&#039;s unique ID in our system | Basic |
| [Article](../DataModel/Overview.md#article) | created | datetime | The date and time at which this article was created | Basic |
| [Article](../DataModel/Overview.md#article) | updated | datetime | The date and time at which this article was updated | Basic |

**Example**:

---
[Back to list](Overview.md)