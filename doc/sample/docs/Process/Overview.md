# Channel Processing Model
The Channel has the following processes available

## Category: Users

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [User Login](UserLogin.md) | Login | This process is triggered when a user wants to login with our application. Upon success, the context is updated with the user information. | [Login Request](UserLogin.md#login-request-event) | user.login |
| [User Logout](UserLogout.md) | Logout | This process is triggered when a user is leaving the application. Upon success, the context is wiped out to prevent its reuse. | [Logout Request](UserLogout.md#logout-request-event) | user.logout |
| [User Registration](UserRegistration.md) | Register | This process is triggered when a user wants to register with our application. Upon success, the user is created internally but is not logged in yet. | [Registration Request](UserRegistration.md#registration-request-event) | user.new |

## Category: Articles

| Business Process | Type | Description | Triggering Event | Message Produced |
| ---------------- | ---- | ------------| ---------------- | ---------------- |
| [New Article Creation](NewArticleCreation.md) | Create | This process is triggered when an author creates a new article. The article will be in &#039;Draft&#039; status until the author decides to submit it for approval. | [New Article](NewArticleCreation.md#new-article-event) | article.new |
| [Article Editing](ArticleEditing.md) | Update | This process is triggered when an author wants to modify one of his existing articles. | [Update Article](ArticleEditing.md#update-article-event) | article.updated |
| [Submit Article for Review](SubmitArticleForReview.md) | Status Update | This process is triggered when an author has finished editing an article and is ready to submit it for review. | [Submit Article](SubmitArticleForReview.md#submit-article-event) | *N/A* |



