# New Article Creation Business Process
This process is triggered when an author creates a new article. The article will be in &#039;Draft&#039; status until the author decides to submit it for approval.

## Overview
 - **Category**: Articles
 - **Type**: Create
 - **Trigger**: [New Article Event](#new-article-event)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| Author | :white_check_mark: | :white_check_mark: | :x: | :x:

## New Article Event
Event triggered when a new article is created by an author
### Data

**Type**: Structure

| BusinessModel | Field | Type | Mandatory | Level |
| ------------- | ----- | ---- | --------- | ----- |
| [`Article`](../DataModel/Overview.md#article) | title | string | yes | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | body | text | yes | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | topic | [`Topic`](../DataModel/Overview.md#topic) | yes | Reference |
| [`Article`](../DataModel/Overview.md#article) | description | text | no | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | labels | List of [`Label`](../DataModel/Overview.md#label) | no | Full |

## Required Data
In order to handle the event above, this process also needs the following data:
### Context Data
Set the article&#039;s author based on the user who triggered the event.

**Type**: Structure

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [`User`](../DataModel/Overview.md#user) | id | uuid | *N/A* |



## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
Save the new article internally

**Type**: Structure

| BusinessModel | Field | Type | Level |
| ------------- | ----- | ---- | ----- |
| [`Article`](../DataModel/Overview.md#article) | title | string | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | description | text | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | body | text | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | status | [`ArticleStatus`](../Dataset/Overview.md#articlestatus) | Reference |
| [`Article`](../DataModel/Overview.md#article) | author | [`User`](../DataModel/Overview.md#user) | Reference |
| [`Article`](../DataModel/Overview.md#article) | topic | [`Topic`](../DataModel/Overview.md#topic) | Reference |
| [`Article`](../DataModel/Overview.md#article) | labels | List of [`Label`](../DataModel/Overview.md#label) | Reference |
| [`Article`](../DataModel/Overview.md#article) | views | List of [`ArticleView`](../DataModel/Overview.md#articleview) | Reference |
| [`Article`](../DataModel/Overview.md#article) | id | uuid | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | created | datetime | *N/A* |
| [`Article`](../DataModel/Overview.md#article) | updated | datetime | *N/A* |


## Returned Data
N/A - *This process does not produce/update any data*

## Messages
### Message article.new
article.new: Message published when a new, draft article has been created by a user

**Data**:

| Variable | Type | BusinessModel | Field | Description | Level |
| -------- | ---- | ------------- | ----- | ----------- | ------|
| title | string | [`Article`](../DataModel/Overview.md#article) | title | Article title | *N/A* |
| description | text | [`Article`](../DataModel/Overview.md#article) | description | Article description | *N/A* |
| body | text | [`Article`](../DataModel/Overview.md#article) | body | The article main body | *N/A* |
| status | [`ArticleStatus`](../Dataset/Overview.md#articlestatus) | [`Article`](../DataModel/Overview.md#article) | status | The article status | Full |
| author | [`User`](../DataModel/Overview.md#user) | [`Article`](../DataModel/Overview.md#article) | author | User who created the article | Full |
| topic | [`Topic`](../DataModel/Overview.md#topic) | [`Article`](../DataModel/Overview.md#article) | topic | Topic to which this article belongs | Full |
| labels | List of [`Label`](../DataModel/Overview.md#label) | [`Article`](../DataModel/Overview.md#article) | labels | List of labels associated with this article by the author | Full |
| id | uuid | [`Article`](../DataModel/Overview.md#article) | id | Business model unique identifier field | *N/A* |
| created | datetime | [`Article`](../DataModel/Overview.md#article) | created | The date and time at which this Article was created | *N/A* |
| updated | datetime | [`Article`](../DataModel/Overview.md#article) | updated | The date and time at which this Article was last updated | *N/A* |

**Example**:

---
[Back to list](Overview.md)