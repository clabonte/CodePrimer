# FunctionalTest Processing Model
The FunctionalTest has the following processes available

## Category: 

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [Synchronous Process No Data](SynchronousProcessNoData.md) | Custom | This is a sample synchronous process that does not require any data as input | [Simple Event](SynchronousProcessNoData.md#simple-event-event) | *N/A* |

## Category: Users

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [User Login](UserLogin.md) | Login | This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information. | [Login Request](UserLogin.md#login-request-event) | user.login |

## Category: Posts

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [Schedule Publication](SchedulePublication.md) | Update | This process is triggered when a user wants to publish a specific post at at given time. | [Schedule Post](SchedulePublication.md#schedule-post-event) | *N/A* |



