# High Level Requirements
After several brainstorming sessions, you and your partners drafted a few high-level requirements for the **Channel application**.

## Application Users and Roles   
Our application will support the following roles:

 - **Anonymous**: Any unauthenticated user accessing our application. They only have access to public content.
 - **Regular Member**: Free registered `User` with the same access as an anonymous user but with a few perks (see below).
 - **Premium Member**: Paying registered `User` who can _**access premium content**_ along with more perks (see below).
 - **Author**: Paying registered `User` who can _**submit**_ new `Post` for distribution in our application.
 - **Admin**: Registered `User` who can manage our application with the ability to _**approve**_, _**reject**_ and _**manage**_ `Post`.

## Article
An `Article` has the following characteristics:
 - Is considered either *standard* or *premium* content as decided by the **Author**.
   - *premium* content can have an *expiry date* after which, the `Article` automatically switches to *standard*.
 - Is in one of the following *states*:
   - *Draft*: **Author** is still working on its `Article`  
   - *Review*: `Article` is pending review.
   - *Pending*: `Article` has been _**approved**_ for _**publishing**_ by an **Admin** on a given *date and time*.
   - *Rejected*: `Article` has been _**rejected**_ with *comments* by an **Admin** and must be reworked by the **Author**. 
   - *Published*: `Article` is published and visible in the application.
   - *Removed*: `Article` has been _**removed**_ by the **Author** or an **Admin**.
 - An `Article` is only visible to **Members** or **Anonymous** users in the *Published* states. 
 - Is _**assigned to a `Topic`**_ to help `User` quickly _**find relevant content**_.
 - Can be _**tagged with multiple `Label`**_ to help `User` _**search for relevant content**_.
 - **Members** and **Authors** can _**comment**_ on an `Article`
 - **Members** and **Authors** can _**cheer**_ on an `Article` to indicate their appreciation.
 
## Topic
A `Topic` has the following characteristics:
 - A `Topic` can only be _**managed**_ by an **Admin**
 - A `Topic` can only be _**deleted**_ if it does not have any `Article` assigned to it.
 - `User` can receive notifications for a given `Topic` as follow:
   - **Regular Member** can only _**subscribe to weekly digests**_ for a single `Topic` of interest.
   - **Premium Member** can _**subscribe to weekly digests**_ for multiple `Topic`s of interest.
   - **Premium Member** can _**subscribe to real-time notifications**_ when an `Article` for pre-defined `Topic` and `Label`s _**is published**_.
   
## Account
Since the purpose of our application is to allow **Authors** to make money, they need to provide them with an `Account` to track activities.

An `Account` has the following characteristics:
 - A *current balance* to track the outstanding amount to _**pay**_ to the **Author** in the next *pay date*.
 - `Payout`s are issued on the *last day of the month if the current balance at least* 20$.
 - A *lifetime earnings* to remind the **Author** about the money made so far.
 - When an *premium* `Article` is _**read**_ for the first time, 0.05$ is _**credited**_ to the **Author**'s account.
 - Each time a *standard* `Article` is _**read**_ by 10 different members (first read only), 0.01$ is _**credited**_ to the **Author**'s account.
 
 
 ## Summary
 We could go on and keep adding various requirements to the list, but we already cover a lot of complex patterns found in modern application. 
 You can hopefully agree that we are far from a typical *Hello World!* example...
 
 The next sections will explain how **CodePrimer** can simplify the architecture and design of such an application in order to generate the backbone of a **production-grade software solution to prime your development team and guide them in the desired direction**.
 
 **Next**: TBD
 
 ---
 Top: [Sample Application](Index.md) | Previous: [Syntax Conventions](Syntax.md) | Next: TBD
