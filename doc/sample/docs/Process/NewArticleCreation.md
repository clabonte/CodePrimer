# New Article Creation Business Process
This process is triggered when an author creates a new article. The article will be in &#039;Draft&#039; status until the author decides to submit it for approval.

## Overview
 - **Category**: Articles
 - **Type**: Create
 - **Trigger**: [New Article Event](#newarticleevent)

| Roles | External Access | Synchronous | Asynchronous | Periodic |
| ----- | --------------- | ----------- | ------------ | -------- |
| Author | :white_check_mark: | :white_check_mark: | :x: | :x:

## New Article Event
Event triggered when a new article is created by an author
### Data
    
| BusinessModel | Field | Mandatory | Level |
| ------------- | ----- | --------- | ----- |
| [Article](../DataModel/Overview.md#article) | title | yes | Basic |
| [Article](../DataModel/Overview.md#article) | body | yes | Basic |
| [Article](../DataModel/Overview.md#article) | topic | yes | Reference |
| [Article](../DataModel/Overview.md#article) | description | no | Basic |
| [Article](../DataModel/Overview.md#article) | labels | no | Full |

## Required Data
In order to handle the event above, this process also needs the following data:
### Context Data
Set the article&#039;s author based on the user who triggered the event.

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../DataModel/Overview.md#user) | id | Basic |



## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
Save the new article internally

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [Article](../DataModel/Overview.md#article) | title | Basic |
| [Article](../DataModel/Overview.md#article) | description | Basic |
| [Article](../DataModel/Overview.md#article) | body | Basic |
| [Article](../DataModel/Overview.md#article) | author | Reference |
| [Article](../DataModel/Overview.md#article) | topic | Reference |
| [Article](../DataModel/Overview.md#article) | labels | Reference |
| [Article](../DataModel/Overview.md#article) | views | Reference |
| [Article](../DataModel/Overview.md#article) | id | Basic |
| [Article](../DataModel/Overview.md#article) | created | Basic |
| [Article](../DataModel/Overview.md#article) | updated | Basic |


## Messages
### Message article.new
article.new: Message published when a new, draft article has been created by a user

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [Article](../DataModel/Overview.md#article) | title | string | Article title | Basic |
| [Article](../DataModel/Overview.md#article) | description | text | Article description | Basic |
| [Article](../DataModel/Overview.md#article) | body | text | The article main body | Basic |
| [Article](../DataModel/Overview.md#article) | author | [User](../DataModel/Overview.md#user) | User who created the article | Full |
| [Article](../DataModel/Overview.md#article) | topic | [Topic](../DataModel/Overview.md#topic) | Topic to which this article belongs | Full |
| [Article](../DataModel/Overview.md#article) | labels | List of [Label](../DataModel/Overview.md#label) | List of labels associated with this article by the author | Full |
| [Article](../DataModel/Overview.md#article) | id | uuid | Article&#039;s unique ID in our system | Basic |
| [Article](../DataModel/Overview.md#article) | created | datetime | The date and time at which this article was created | Basic |
| [Article](../DataModel/Overview.md#article) | updated | datetime | The date and time at which this article was updated | Basic |

**Example**:
