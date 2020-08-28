# FunctionalTest Dataset
The following datasets are available to use in your application Data Model

## Table of Contents
- [UserStatus](#userstatus)
- [Plan](#plan)

## `UserStatus`
List of statuses that can be associated with a User

### Structure

| Field | Type | Description | Identifier | Unique Values |
| ----- | ---- | ----------- | ---------- | ------------- |
| **name** | string | The name of the status | yes | yes |
| **description** | string | A description of what this status means | no | yes |
| **loginAllowed** | boolean | Whether this status allows the user to log on our application | no | no |

### Elements

| name | description | loginAllowed |
| ---- | ----------- | ------------ |
| **registered** | User is registered but has not confirmed his email address yet | yes |
| **active** | User is fully registered and allowed to user our application | yes |
| **canceled** | User has canceled his subscription with our application | no |
| **locked** | User has been locked due to too many failed login attempts | no |

---
<br/><br/>
## `Plan`
List of plans that can be purchased in our application along with their access

### Structure

| Field | Type | Description | Identifier | Unique Values |
| ----- | ---- | ----------- | ---------- | ------------- |
| **id** | id | Unique ID to use for this plan | yes | yes |
| **name** | string | The name associated with this plan, as presented to users and prospects | no | yes |
| **description** | string | A description of the plan, as presented to users and prospects | no | yes |
| **internal** | boolean | Whether this plan can only be used internally or available for purchase | no | no |
| **active** | boolean | Whether this plan can still be used for new/upgraded accounts | no | no |
| **monthlyPrice** | price | The selling price for a contract renewable on a monthly basis | no | no |
| **annualPrice** | price | The selling price for a contract renewable on a yearly basis | no | no |
| **premiumAccess** | boolean | Whether this plan provides access to premium content | no | no |
| **editingAccess** | boolean | Whether this plan provides access to editing content | no | no |
| **adminAccess** | boolean | Whether this plan provides access to admin functionality | no | no |

### Elements

| id  | name | description | internal | active | monthlyPrice | annualPrice | premiumAccess | editingAccess | adminAccess |
| --- | ---- | ----------- | -------- | ------ | ------------ | ----------- | ------------- | ------------- | ----------- |
| **1** | Admin | Internal plan used to manage the application | yes | yes | 0 | 0 | yes | yes | yes |
| **2** | Free | Free plan giving access to basic functionality to registered users | no | yes | 0 | 0 | no | no | no |
| **3** | Premium | Premium plan giving access to premium functionality to registered users | no | yes | $5 | $50 | yes | no | no |
| **4** | Author | Premium plan giving access to premium and editing functionality to registered users | no | yes | $10 | $100 | yes | yes | no |

---
<br/><br/>
