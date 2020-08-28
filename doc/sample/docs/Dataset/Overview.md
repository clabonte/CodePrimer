# Channel Dataset
The following datasets are available to use in your application Data Model

## Table of Contents
- [UserRole](#userrole)
- [UserStatus](#userstatus)
- [ArticleStatus](#articlestatus)

## `UserRole`
Roles that can be assigned to our application users

### Structure

| Field | Type | Description | Identifier | Unique Values |
| ----- | ---- | ----------- | ---------- | ------------- |
| **name** | string | The role name, as used in our authorization scheme | yes | yes |
| **description** | string | A high-level description of what this role is used for | no | yes |
| **fullAccess** | boolean | Whether this role provides full access to the solution | no | no |

### Elements

| name | description | fullAccess |
| ---- | ----------- | ---------- |
| **admin** | Role reserved to internal employees with full access | yes |
| **author** | Role reserved to registered users who can submit articles in our application | no |
| **premium** | Role reserved to registered users with a paying subscription in our application | no |
| **member** | Role reserved to registered users under the free plan | no |

---
<br/><br/>
## `UserStatus`
Status that can be assigned to a user

### Structure

| Field | Type | Description | Identifier | Unique Values |
| ----- | ---- | ----------- | ---------- | ------------- |
| **name** | string | The user status | yes | yes |
| **description** | string | A high-level description of what this status represents | no | yes |
| **accessAllowed** | boolean | Whether the user can access the application with this status | no | no |

### Elements

| name | description | accessAllowed |
| ---- | ----------- | ------------- |
| **active** | User is active and allowed to access the application. | yes |
| **pending** | User registration has started but not completed yet. He is not allowed to access the application until registration is complete | no |
| **locked** | User is locked out after too many false attempts. User must either reset his password or wait until the account is automatically unlocked after a pre-configured delay. | no |
| **canceled** | User account has been canceled at the user request | no |

---
<br/><br/>
## `ArticleStatus`
Status that can be assigned to an article

### Structure

| Field | Type | Description | Identifier | Unique Values |
| ----- | ---- | ----------- | ---------- | ------------- |
| **name** | string | The article status | yes | yes |
| **description** | string | A high-level description of what this status represents | no | yes |

### Elements

| name | description |
| ---- | ----------- |
| **draft** | Article is still being worked on by the author. |
| **review** | Article has been submitted for approval by the author. |
| **pending** | Article has been approved for publishing by an Admin on a given date and time. |
| **rejected** | Article has been rejected with comments by an Admin and must be reworked by the Author. |
| **published** | Article is published and visible in the application. |
| **removed** | Article has been removed by the Author or an Admin. |

---
<br/><br/>
