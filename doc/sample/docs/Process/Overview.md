# Channel Processing Model
The Channel has the following processes available

## Category: Users

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [User Login](UserLogin.md) | Login | This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information. | Login Request | user.login |
| [User Logout](UserLogout.md) | Logout | This process is triggered when a user is leaving the application. Upon success, the context is wiped out to prevent its reuse. | Logout Request | user.logout |
| [User Registration](UserRegistration.md) | Register | This process is triggered when a user wants to register with our application. Upon success, the user is created internally but is not logged in yet. | Registration Request | user.new |

## Category: Articles

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [New Article Creation](NewArticleCreation.md) | Create | This process is triggered when an author creates a new article. The article will be in &#039;Draft&#039; status until the author decides to submit it for approval. | New Article | article.new |



