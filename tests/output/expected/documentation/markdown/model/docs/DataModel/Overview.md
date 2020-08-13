# FunctionalTest Data Model

## Table of Contents
- [User](#user)
- [UserStats](#userstats)
- [Metadata](#metadata)
- [Post](#post)
- [Topic](#topic)
- [Subscription](#subscription)

## `User`
This entity represents a user

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **firstName** | string | User first name | no | *Empty* | John | yes | no |
| **lastName** | string | User last name | no | *Empty* | Doe | yes | no |
| **nickname** | string | The name used to identify this user publicly on the site | no | *Empty* | JohnDoe | yes | yes |
| **email** | email | User email address | yes | *N/A* |  | yes | yes |
| **password** | password | User password | yes | *N/A* |  | no | no |
| **crmId** | string | The ID of this user in our external CRM | no | *Empty* | 2c3b1c3e-b29c-4564-80c4-e4b95cfbfc81 | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **stats** | [`UserStats`](#userstats) | User login statistics  | OneToOne *(unidirectional - left)* |
| **subscription** | [`Subscription`](#subscription) | The plan to which the user is subscribed  | OneToOne *(bidirectional - left)* |
| **metadata** | List of[`Metadata`](#metadata) | Extra information about the user  | OneToMany *(unidirectional - left)* |
| **posts** | List of[`Post`](#post) | Blog posts created by this user  | OneToMany *(bidirectional - left)* |
| **topics** | List of[`Topic`](#topic) | List of topics this user to allowed to create posts for  | ManyToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | The user&#039;s unique ID in our system | yes | b34d38eb-1164-4289-98b4-65706837c4d7 | no | no |
| **created** | datetime | The date and time at which this user was created | no |  | no | no |
| **updated** | datetime | The date and time at which this user was updated | no |  | no | no |
| **activationCode** | randomstring | The code required to validate the user&#039;s account | no | qlcS7L | no | no |

---
<br/><br/>
## `UserStats`
Simple statistics about the user

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **firstLogin** | datetime | First time the user logged in the system | no | *Empty* |  | no | no |
| **lastLogin** | datetime | Last time the user logged in the system | no | *Empty* |  | no | no |
| **loginCount** | long | Number of time the user logged in the system | no | *Empty* |  | no | no |

---
<br/><br/>
## `Metadata`
Variable set of extra information

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **name** | string | The name to uniquely identify this metadata | yes | *N/A* |  | no | no |
| **value** | text | The value associated with this metadata | yes | *N/A* |  | no | no |

---
<br/><br/>
## `Post`
Post created by the user

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **title** | string | The post title | yes | *N/A* |  | no | no |
| **body** | text | The post body | yes | *N/A* |  | no | no |
| **scheduled** | datetime | The time at which this post must be published | no | *Empty* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **author** | [`User`](#user) | The user who created this post  | OneToMany *(bidirectional - right)* |
| **topic** | [`Topic`](#topic) | The topic to which this post belongs  | OneToMany *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **id** | uuid | The post&#039;s unique ID in our system | yes | deadbeef-1164-4289-98b4-65706837c4d7 | no | no |
| **created** | datetime | Time at which the post was created | no |  | no | no |
| **updated** | datetime | Last time at which the post was updated | no |  | no | no |

---
<br/><br/>
## `Topic`
A topic regroups a set of posts made by various authors

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **title** | string | The topic title | yes | *N/A* |  | no | no |
| **description** | text | The topic description | no | *Empty* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **authors** | List of[`User`](#user) | List of authors who are allowed to post on this topic  | ManyToMany *(bidirectional - right)* |
| **posts** | List of[`Post`](#post) | List of posts published on this topic  | OneToMany *(bidirectional - left)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **created** | datetime | Time at which the post was created | no |  | no | no |
| **updated** | datetime | Last time at which the post was updated | no |  | no | no |

---
<br/><br/>
## `Subscription`
The subscription bought by a user to user our services

### Business Attributes

| Name | Type | Description | Mandatory | Default | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ------- | ---------- | ------ |
| **plan** | string | The plan subscribed by this user in our billing system | yes | *N/A* |  | no | no |
| **renewal** | date | The date at which the subscription must be renewed | yes | *N/A* |  | no | no |

### Business Relations

| Name | Type | Description | Relationship |
| ---- | ---- | ----------- | ------------ |
| **user** | [`User`](#user) | The user to which this subscription belongs  | OneToOne *(bidirectional - right)* |

### Managed Fields
The following fields are automatically managed by the backend and cannot be modified by the user.

| Name | Type | Description | Mandatory | Example | Searchable | Unique |
| ---- | ---- | ----------- | --------- | ------- | ---------- | ------ |
| **created** | datetime | Time at which the post was created | no |  | no | no |
| **updated** | datetime | Last time at which the post was updated | no |  | no | no |

---
<br/><br/>
