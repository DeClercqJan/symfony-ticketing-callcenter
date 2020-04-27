# TICKETING MOSHI MOSHI CALLCENTER 

## BASIC IDEA

## IMPLEMENTATION 
- took time out to schematize: read the assigment and tried to separate the entities and their properties on the one hand and, on the other, listed the actions users can take as I'm planning to assign them to several users (and reuse them whilst doing so) 
- scheming was far from easy and probably will need to change stuff, which will get messy. May even prove to have been the wrong approach
- for basic set-up, I have 3 entitites. Users and tickets in ManyToMany relation (as a Customer-user can have many tickets and a ticket can have both a Customer-user and a Agent-user) and comments: easy; 1 comments, 1 user/author, 1 ticket; 1 ticket can have many comments; 1 user can have many comments
- (I was, orginally, gonna extend User class to Customer and Agent classes and NOT have a ManyToMany-relationship, but didn't (immediately) find how Doctrine would react so for now I'm relying on the roles-property
- timestampable trait, which I'm expecting to need in order to calculate whether or not the comment made by a user in response to a request for feedback is the right one in order to continue the track
- will start out with the 'perfect' lifecycle a ticket can follow. That excellent service we all expect and never experiences
- will rely heavily on Symfonycasts code
- gonna use basic article/id123/edit -like routing structure
- getRoles always returns ROLE_USER
- fixtures for dummy data
- made functional login form using maker using make:auth and tinkering a bit in security.yaml and such. But can't see username of logged in user. Moving on) 
- working mailer, using symfony/mailer bundle and mailtrap to debug. Now gonna configure symfonycasts' reset-password-bundle
- registration form using make:registration-form
- welcome mail to all newly registered users (need to change, because this was meant for new agents - see assignment below)
-added data transformer to form in order to add roles to new User. However, no real data yet"

belangrijke les: die inversed by wel belangrijk bv. in het geval toen ik een new Ticket wou onmaken, selecteerde ik wel een user, maar werd niet opgeslagen
dan users property van ticket de annotatie veranderd naar inversed by (in plaat svan mapped by) en toen werkte het-

## TO DO'S
- check routes. Need logical path for when not logged in and such and redirect to previous RELEVANT page. Think I'm also employing hard coded redirects sometimes and sometime that trait TargetPath or something
-created dev branch to try out make:crud
- need to hash password of newly created users
- need to selectively display properties: serializer ...
- creating new user doesn work bidirectionally with tickets. 
    The mappings App\Entity\User#tickets and App\Entity\Ticket#users are inconsistent with each other.
 	 The association App\Entity\Ticket#users refers to the inverse side field App\Entity\User#users which does not exist.
 php bin/console doctrine:schema:validate
? Tried something with @ORM\JoinTable(name="ticket_user")
? spelling mistake? user vs users
? inversedby vs mappedby
-> check my notes, bv. voor php-doctrine oefening met api enzo
-> check https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html

-merge branch in master branch mergen0

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
