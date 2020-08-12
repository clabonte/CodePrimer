# Channel Data Model

## Table of Contents
- [User](#user)
- [Article](#article)
- [ArticleView](#articleview)
- [Topic](#topic)
- [Label](#label)
- [SuggestedLabel](#suggestedlabel)
- [Account](#account)
- [Interest](#interest)
- [Transaction](#transaction)
- [Payout](#payout)

## `User`
A registered used in our application

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **firstName** | string | User first name | no | *Empty* | John | yes | no |
| **lastName** | string | User last name | no | *Empty* | Doe | yes | no |
| **nickname** | string | The name used to identify this user publicly in the application | no | *Empty* | JohnDoe | yes | yes |
| **email** | email | User email address | yes | *N/A* | john.doe@test.com | yes | yes |
| **password** | password | User password to access our application | yes | *N/A* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **account** | [`Account`](#account) | User&#039;s account to track earnings  | OneToOne *(bidirectional - left)* |
| **articles** | List of[`Article`](#article) | List of articles owned by this user  | OneToMany *(bidirectional - left)* |
| **views** | List of[`ArticleView`](#articleview) | List of articles viewed by this user  | OneToMany *(bidirectional - left)* |
| **interests** | List of[`Interest`](#interest) | List of topics user is interested in  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **role** | string | User role in the application | yes | member | yes | no |
| **status** | string | User status | yes | active | yes | no |
| **id** | uuid | User&#039;s unique ID in our system | yes | b34d38eb-1164-4289-98b4-65706837c4d7 | no | no |
| **created** | datetime | The date and time at which this user was created | no |  | no | no |
| **updated** | datetime | The date and time at which this user was updated | no |  | no | no |

---
<br/><br/>
## `Article`
An article in our application

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **title** | string | Article title | no | *Empty* | How to go from idea to production-ready solution in a day with CodePrimer | yes | no |
| **description** | text | Article description | no | *Empty* | This article explains how architects can save days/weeks of prepare to get a production-grade application up and running using the technology of their choice. | yes | no |
| **body** | text | The article main body | no | *Empty* |  | yes | no |
| **status** | string | The article status | yes | *N/A* | draft | yes | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **author** | [`User`](#user) | User who created the article  | OneToMany *(bidirectional - right)* |
| **topic** | [`Topic`](#topic) | Topic to which this article belongs  | OneToMany *(bidirectional - right)* |
| **labels** | List of[`Label`](#label) | List of labels associated with this article by the author  | ManyToMany *(bidirectional - left)* |
| **views** | List of[`ArticleView`](#articleview) | List of views associated with this article  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | Article&#039;s unique ID in our system | yes | 22d5a494-ad3d-4032-9fbe-8f5eb0587396 | no | no |
| **created** | datetime | The date and time at which this article was created | no |  | no | no |
| **updated** | datetime | The date and time at which this article was updated | no |  | no | no |

---
<br/><br/>
## `ArticleView`
An article view action by a registered user

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **count** | integer | Number of times this user viewed this article | no | *1* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **user** | [`User`](#user) | User who viewed the article  | OneToMany *(bidirectional - right)* |
| **article** | [`Article`](#article) | Article associated with the view  | OneToMany *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **created** | datetime | The date and time at which this article was viewed the first time by this user | no |  | no | no |
| **updated** | datetime | The date and time at which this article was viewed the last time by this user | no |  | no | no |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `Topic`
A high level topic that can be used to categorize articles

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **name** | string | A topic short description | yes | *N/A* | Technology | yes | yes |
| **description** | string | A description of what kind of articles should be associated with | yes | *N/A* | Articles related to the latest trends in Technology to keep you up to date | yes | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **articles** | List of[`Article`](#article) | List of articles associated with this topic  | OneToMany *(bidirectional - left)* |
| **suggested labels** | List of[`SuggestedLabel`](#suggestedlabel) | List of labels that are often associated with this topic  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `Label`
A tag that can be associated with an article by an author to help in its classification

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **tag** | string | A unique tag | yes | *N/A* | PHP | yes | yes |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **articles** | List of[`Article`](#article) | List of articles associated with this tag  | ManyToMany *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `SuggestedLabel`
Labels often associated with a given topic

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **count** | integer | Number of times this label has been associated with this topic | no | *1* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **label** | [`Label`](#label) | Label associated with this suggestion  | OneToOne *(unidirectional - left)* |
| **topic** | [`Topic`](#topic) | Topic associated with this suggestion  | OneToMany *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `Account`
Author account to track earnings

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **balance** | price | Current amount owed to the author | no | *Empty* | 9.90$ | yes | no |
| **lifetime** | price | Lifetime earnings associated with this account | no | *Empty* | 200$ | yes | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **member** | [`User`](#user) | Member associated with this account  | OneToOne *(bidirectional - right)* |
| **topic** | [`Topic`](#topic) | Topic to which this article belongs  | OneToOne *(unidirectional - left)* |
| **payouts** | List of[`Payout`](#payout) | List of payouts already made to the user  | OneToMany *(bidirectional - left)* |
| **transactions** | List of[`Transaction`](#transaction) | List of transactions used to track earnings details  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | Account&#039;s unique ID in our system | yes | b34d38eb-1164-4289-98b4-65706837c4d7 | no | no |
| **created** | datetime | The date and time at which this account was created | no |  | no | no |
| **updated** | datetime | The date and time at which this account was updated last | no |  | no | no |

---
<br/><br/>
## `Interest`
Interest expressed by a user to be notified of new articles

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **instantNotification** | boolean | Whether the user wants to be notified ASAP when a new article matching this interest is published | no | ** |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **member** | [`User`](#user) | User who expressed the interest  | OneToMany *(bidirectional - right)* |
| **label** | [`Label`](#label) | Label associated with this interest  | OneToOne *(unidirectional - left)* |
| **topic** | [`Topic`](#topic) | Topic associated with this interest  | OneToOne *(unidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `Transaction`
An article view that is tied with some earnings

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **amount** | price | Earnings associated with this transaction | yes | *N/A* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **account** | [`Account`](#account) | Account associated with this transaction  | OneToMany *(bidirectional - right)* |
| **articleView** | [`ArticleView`](#articleview) | ArticleView that triggered the transaction  | OneToOne *(unidirectional - left)* |
| **payout** | [`Payout`](#payout) | The payout associated with this transaction, set once the payout is issued  | OneToMany *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **created** | datetime | The date and time at which this transaction was viewed the first time by this user | no |  | no | no |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
## `Payout`
Tracks payment made to an author

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **amount** | price | Amount associated with this payout | yes | *N/A* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **account** | [`Account`](#account) | Account associated with this transaction  | OneToMany *(bidirectional - right)* |
| **transactions** | List of[`Transaction`](#transaction) | The list of transactions associated with this payout  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **created** | datetime | The date and time at which this payment was issued | no |  | no | no |
| **updated** | datetime | The date and time at which this payment was updated last | no |  | no | no |
| **id** | uuid | DB unique identifier field | yes |  | no | no |

---
<br/><br/>
