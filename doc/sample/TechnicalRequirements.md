# Sample Application - High Level Technical Requirements
Unlike the business requirements, when it was time to discuss with your business partners about technical requirements, they made it short and sweet... All they could come up with was:

> We need a *'wall of servers'* to make sure we can support the huge level of traffic **Channel** will generate. 
> We've heard about this *cloud* concept, it may be a good place to *'raise our wall'*. You should look at it...

Just when you realized that, *once again*, you're on your own, one of your partners dropped:

> And, by the way, we would rather spend our limited budget on my super cool advertising concept to attract users when we launch the product than on some geeks stuff that nobody cares about... 
> So you have the equivalent of a cheap *'panel'* budget to build our *'wall'*... 

Great! More good news coming your way... Plan for huge capacity using only a tiny budget. Not exactly the most compatible requirements...

You always enjoy challenges, this is what drives you. Your gut feeling tells you will be well served with this project!!

# Overall Approach
Without useful notes from your partners discussion to refer to, you grab a cup of dark coffee, find a quiet place and sit on your own to draft a plan of action on this one...

The first thing you are considering is whether to start this project with a monolithic or microservices approach.
You know the strengths and weaknesses of both, so you list them out on a piece of paper to get your brain started...


| Consideration | Monolith | Microservices |
| ------------- | -------- | -------------- |
| **Codebase** | **Single codebase** with everything in it :white_check_mark: | Multiple repositories |
| **Testing** | **Easier** to test :white_check_mark: | Requires **more planning** and structure |
| **Deployment** | **Easier** to deploy :white_check_mark: | Requires **more planning** and structure |
| **Scalability** | Single load balancer required :white_check_mark: | Requires **more planning** and structure based on services type |
| **Monitoring** | **Easier** to monitor :white_check_mark: | Requires **more planning** and structure based on services type |
| **Debugging** | **Easier** to debug :white_check_mark: | Requires **more work** and structure to investigate **complex issues** :x: |
| **Impact of error introduction** | Error may **impact whole application** :x: | **Limited impact**, errors tend to be **isolated to a service** :white_check_mark: |
| **Code evolution** | Perfect recipe for **spaghetti**!! :x: | Forces developers to **maintain cleaner structure** :white_check_mark: |
| **Code complexity** | Simple to start with, **complexity increases** over time :x: | Complexity **remains simple** for each service over time :white_check_mark: |
| **Infrastructure complexity** | **Simple** to understand :white_check_mark: | Complex to start with, **complexity increases** over time :x: |
| **Future-proofing** | <ul><li>Framework upgrades: **painful** :x:</li><li>Technology migration: **full rewrite!!** :x: :x:</li></ul> | **Easy** to plan the introduction of new technology :white_check_mark: | 
| **Best suited for** | <ul><li>Early stage and prototypes</li><li>Simple projects</li><li>Small team</li><li>Junior developers</li></ul> | <ul><li>Complex applications</li><li>Quickly evolving solutions</li><li>Large team</li><li>Experienced developers</li></ul>
   
General wisdom suggests to **start with a monolith for the MVP** and to **evolve towards a microservices architecture** over time when the need arises. Based on your experience, you know that is **easier said than done**, because at this point in time, you will need to:
 - **Train your development team** on this new architecture along with advanced DevOps concepts.
 - Plan to **pause (or at least slow down significantly) the evolution** of the application at some point or,
 - Hire more developers to work on the migration while your existing team keeps working on the **now legacy** code
   - Don't forget that _**very few developers want to work on legacy code...**_   

**Even if you warn** your business partners upfront about this and **they agree on your plan, they won't remember it** when the need to make this move arises! And this will likely **happen at the worst time** possible, in the middle of a rush in order to deliver that new and shiny feature to perform the **business pivot desparately needed by your partners**...

> Wouldn't be better if there was an alternative or, at least, a better way to plan and execute this transition?

Lately, you came across CodePrimer and its [Business Bundle approach](../bundle/Overview.md) and you think it might be worth to take a look and give it a try for this project...

# Infrastructure Overview
When you started thinking about the infrastructure to put together for this project, you went back to your notes about the overall approach to adopt because your choice will directly impact the infrastructure to use.

Nevertheless, you take a few notes about some characteristics you are looking for:
 - Easy to deploy, ideally using **continuous deployment** techniques.
 - Easy to scale (up and down), ideally using **autoscaling** strategies available in cloud environments.
 - Easy to monitor, ideally using **cloud-based monitoring platforms**.
 - Easy to backup and recover to handle **unforseen events**.
 - Easy to upgrade to ensure you keep your systems up to date to **minimize your exposure to security attacks**.
 - **Enforce strict access rules** to prevent unauthorized access and data leaks.

Looking at your list, it is clear you should host your solution on a top-tier cloud provider to simplify your operations and focus your energy on build the application, not the low-level infrastructure to host it.

Based on your work with several top-tier cloud providers, you know they can all meet your needs so you can postpone to later which one you will pick.

**Next**: [Application Data Model](DataModel.md)
 
 ---
 Top: [Sample Application](Index.md) | Previous: [Business High Level Requirements](BusinessRequirements.md) | Next: [Application Data Model](DataModel.md)
 