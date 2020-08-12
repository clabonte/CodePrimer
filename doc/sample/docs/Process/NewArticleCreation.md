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
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | title | yes | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | body | yes | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | topic | yes | Reference |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | description | no | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | labels | no | Full |

## Required Data
In order to handle the event above, this process also needs the following data:
### Context Data
Set the article&#039;s author based on the user who triggered the event.

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [User](../../../../../doc/sample/docs/DataModel/User.md) | id | Basic |



## Produced Data
Upon successful completion, this process will produce/update the following data:

### Internal Data
Save the new article internally

| BusinessModel | Field | Level |
| ------------- | ----- | ----- |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | title | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | description | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | body | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | author | Reference |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | topic | Reference |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | labels | Reference |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | views | Reference |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | id | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | created | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | updated | Basic |


## Messages
### Message article.new
article.new: Message published when a new, draft article has been created by a user

**Data**:

| BusinessModel | Field | Type | Description | Level |
| ------------- | ----- | ---- | ----------- | ------|
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | title | string | Article title | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | description | text | Article description | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | body | text | The article main body | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | author | [User](../../../../../doc/sample/docs/DataModel/User.md) | User who created the article | Full |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | topic | [Topic](../../../../../doc/sample/docs/DataModel/Topic.md) | Topic to which this article belongs | Full |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | labels | List of [Label](../../../../../doc/sample/docs/DataModel/Label.md) | List of labels associated with this article by the author | Full |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | id | uuid | Article&#039;s unique ID in our system | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | created | datetime | The date and time at which this article was created | Basic |
| [Article](../../../../../doc/sample/docs/DataModel/Article.md) | updated | datetime | The date and time at which this article was updated | Basic |

**Example**:
