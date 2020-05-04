# TICKETING MOSHI MOSHI CALLCENTER 

## BASIC IDEA
- customer make tickets for service requests. These these are then distruted over the helpdesk's employees and resolved. See requirements below
- PHP Symfony
- Moshi moshi just because I like the sound
 
## IMPLEMENTATION

### SETUP
- branching: master, dev, local branches to try stuff out
- tried to separate the entities and their properties on the one hand and, on the other, listed the actions users can take as I'm planning to assign them to several users (and reuse them whilst doing so) 
- took much from https://symfonycasts.com/screencast/symfony-security

### ENTITIES 
- Users and tickets in ManyToMany relation (as a Customer-user can have many tickets and a ticket can have both a Customer-user and a Agent-user) and comments: easy; 1 comments, 1 user/author, 1 ticket; 1 ticket can have many comments; 1 user can have many comments
- storing real users as authors of tickets (so tickets now have 2 properties in different relations with users) 
- make:crud 
- tried constants in entity ticket for stuff like  "open" status message wthat will be used in if-checks in order to reduce typo's
- for business logic: it is now possible to create a ticket without assigning a user. These are displaybed in admin ticket index to be displayed in some admin section in order to be reassigned
- reopening a ticket also allows for writing a comment. Businessly, this makes sense to me, but does not yet display the way I want it 

### ACCESS
- user roles correspond to a series of authorizations. See security.yaml. Custom voter per object checks access. Could have done this directly, but now there's a centralized place at least so that the check if user is author is also visible at an eye's glance. Also displaying some things and not others in TWIG
- getRoles always returns ROLE_CUSTOMER. in security. yaml I may repeat myself in role hierarchy,
- switch user for easy back-and-forth checking of how things react on permissions
- made functional login form using maker using make:auth and tinkering a bit in security.yaml and such. 
- registration form using make:registration-form
- symfonycasts' reset-password-bundle
- working mailer, using symfony/mailer bundle and mailtrap to debug.

### ROUTING 
- basic article/id123/edit -like routing structure Appending commentcontroller mostly
- privileging hierarchical ticket > comments, but also trying to provide overview of all comments while restricting/allowing actions in order to keep things logical. Notably, one can't create new comment from generic comment overviewpage
    
### TWIG
- as always, paid very little attention to style. Was an ends to a mean, to debug really

## TO DO'S
- basically, for learning purposes, this exercise is done. What's left are basically variations of the same thing and while practice does make perfect, I have other priorities
- in hindsight, this would be a candidate to try state machine pattern out. 

- notable to do's:
- dashboard: numbers, order tickets according to priorities
- public/private display
- Second line agents
- /reopen still has too many fields: how can I add only one row to existing collection without rendering the collection? 
    Tried with event listeners, but was trial-and-error and error ultimately.
    ! not rendering to twig, may result in overwriting all data of other comments instead of just adding one. Therefore, I've chosen to display all in order to make this clear at least. 
    Also, jquery rendering is another thing
    perhaps this library can help: https://github.com/a2lix/symfony-collection 
- how to redirect to previous page (here: multiple pages pointing to comment edit endpoint)? TargetPathTrait appears to be for login. Tried passing hidden input field to form, but the medecine seems worse than the cure: https://stackoverflow.com/questions/51498650/redirect-two-steps-back-in-symfony
- should probably rename the users property of tickets to agents or so. Also maybe limit it to 1 instead of multiple. Easier workflow, I think
- welcome mail now all newly registered users (need to change, because this was meant for new agents - see assignment below)
- in several controller using mailer in various ways. Can I put this in some service and streamline it?
- testing by way of customer/agent/admin journeys/routes and/or ticket journey's (different perspective). Take a piece of paper and start drawing lines
- maybe extend User class to Customer and Agent classes and NOT have a ManyToMany-relationship with Ticket instead of using roles


# ORIGINAL ASSIGNMENT BELOW

# Title: workflow

- Repository: `challenge-workflow`
- Type of Challenge: `Consolidation Challenge`
- Duration: `5 days`
- Deployment strategy : `heroku`
- Team challenge : `group`

## Learning objectives

## The Mission
Until now we have mainly been writing CRUD applications, where the main focus is on storing and display data.

Another big part of development is workflow implementation to automate processes in the real world. Any time you have a status field (for example is an invoice paid? Is a product delivered?) you probably have a workflow before you.

### A story about a tomato
Think about a physical product like a tomato in a grocery store, this tomato lives an entire journey:

- The tomato is harvested on the farm
- The tomato is shipped with a truck to a store
- Their the tomato is weighted and gets a price. Extra information about the tomato is now available.
- Maybe the store keeps a ledger of the total amount of tomatoes in their store - they have to update this information.
- The tomato is sold - nothing more can be done with it.
- The ledger amount of tomatoes is lowered again.
- Maybe if the amount becomes too low a new shipment of tomatoes is ordered.

Also interesting to note is that there can be several endings or routes the tomato can travel. Maybe at any time in the flow somebody can say that the tomato has fungus on it - taking it out of the market and flow.

Maybe sometimes the farmer sells the products directly to some customers, in which case we can skip the transportation step.

![Tomato](tomato.jpg)

## Must-have features
This week we are going to implement the workflow of a call center. 

We have 5 different users in our system that have different abilities:

### Guest
- Can register himself to become a Customer.
- Can login to become another role (based on credentials)
- Provide a "forgot my password" functionality

### Customer
- A customer can create a ticket on the site. It get's the status "open".
- The customer can see all open tickets that he created and comment on them.
- If the customer responds to a ticket with status "Waiting for customer feedback" mail the agent and change the status again to "in progress".
- A customer can reopen a ticket if it has been closed for less than 1 hour.

### Agent
- First line agents can see all open tickets and assign one to themselves. It now get's the status "in progress".
- Agents can leave comments on a ticket which can be public (the customer can see the comment and react) or private (invisible for customer)
- If the agent leaves a public comment mark the ticket "Waiting for customer feedback"
- First line agents can "escalate a ticket" to "second line" help.
- An agent can close a ticket if it has at minimum one agent comment (to explain the solution to the customer).

### Second line agent
- Second line agents can do everything a first line agent can do, but only for escalated tickets.

### Manager
- A manager can create new agents or change the details of an agent (first or second line help). When a new agent is created sent a welcome e-mail to the agent, with a link where the agent can configure his password. You could reuse logic of the "forgot my password" guest flow here.
- Provide a dashboard where managers can see statistics about the agents:
    - Number of open tickets
    - Number of closed tickets
    - Number of tickets that where reopened
    - A percentage comparision between the 2 numbers above.
- A manager can re-assign tickets or mark them as "won't fix". In the last case the ticket is considered closed and cannot be opened by the customer later on. You should provide a required field to enter a reason for the manager why he will not fix it.

- Managers can with one button de-assign all tickets, they once again get the status "open".
They normally do this at the end of every working day to prevent tickets being locked by an agent who is sick the next day.
- Managers can assign priorities, on which the tickets should be sorted.

### General rule
- Every time a ticket is updated (comment, status change) you have to mail the customer EXCEPT when a private comment is made.

## Nice to have features
- Your imagination is the limit!