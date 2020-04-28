# TICKETING MOSHI MOSHI CALLCENTER 

## BASIC IDEA

## IMPLEMENTATION (- points, * subelements, X lessons)
- took time out to schematize: read the assigment and tried to separate the entities and their properties on the one hand and, on the other, listed the actions users can take as I'm planning to assign them to several users (and reuse them whilst doing so) 
- scheming was far from easy and probably will need to change stuff, which will get messy. May even prove to have been the wrong approach
- for basic set-up, I have 3 entitites. Users and tickets in ManyToMany relation (as a Customer-user can have many tickets and a ticket can have both a Customer-user and a Agent-user) and comments: easy; 1 comments, 1 user/author, 1 ticket; 1 ticket can have many comments; 1 user can have many comments
- (I was, orginally, gonna extend User class to Customer and Agent classes and NOT have a ManyToMany-relationship, but didn't (immediately) find how Doctrine would react so for now I'm relying on the roles-property
- timestampable trait, which I'm expecting to need in order to calculate whether or not the comment made by a user in response to a request for feedback is the right one in order to continue the track
- will start out with the 'perfect' lifecycle a ticket can follow. That excellent service we all expect and never experiences
- will rely heavily on Symfonycasts code
- gonna use basic article/id123/edit -like routing structure
- getRoles always returns ROLE_CUSTOMER. in security. yaml I may repeat myself in role hierarchy, but I think it's better for clarity as one (me) easily forgets that all registered users have the (related) roles of the customer
- fixtures for dummy data
- made functional login form using maker using make:auth and tinkering a bit in security.yaml and such. But can't see username of logged in user. Moving on) 
- working mailer, using symfony/mailer bundle and mailtrap to debug. Now gonna configure symfonycasts' reset-password-bundle
- registration form using make:registration-form
- welcome mail to all newly registered users (need to change, because this was meant for new agents - see assignment below)
- added data transformer to form in order to add roles to new User. However, no real data yet"
- created dev branch to try out make:crud in the hopes of facilitating set-up and have a good base to tinker with
- massive problems creating new user doesn work bidirectionally with tickets
*The mappings App\Entity\User#tickets and App\Entity\Ticket#users are inconsistent with each other.
 The association App\Entity\Ticket#users refers to the inverse side field App\Entity\User#users which does not exist.
 spelling mistake? user vs users
 inversedby vs mappedby
X OK die mapped by en inversedby annotation s lijkt me te verwijzen naar de property(!)naam in de andere entity. Geen fout in ieder geval meer)
* ook als ik user wil verwijderen nu:
 "You cannot refresh a user from the EntityUserProvider that does not contain an identifier. The user object has to be serialized with its own identifier mapped by Doctrine."
X OK was omdat ik mezelf (ingelogde gebruiken) verwijderde
X entity with inversedBy above a property is the owning side (mapped by is inverse side): ticket (makes more sense: Whenever you add a new article, you want to connect it with existing or new tags. 
check https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html
https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/association-mapping.html#many-to-many-bidirectional
X after many hours I created a different branch in order to follow step of https://symfonycasts.com/screencast/collections/saving-inverse-side-collection :
"We didn't need to add a lot of code to get this to work... but this situation has caused many developers to lose countless hours trying to get their relationship to save. To summarize: if you're modifying the inverse side of a relationship, set the by_reference form option to false, create an adder and remover function, and make sure you set the owning side of the relationship in each. That is it."
X opgelost: in formulier moest ik naar klasse verwijzen en reference naar false zetten in de reverse side. Zonder die multple werkt het gelijk ook neit. Kwestie van functionaliteit betekent het dat men meerdere Tickets kan toewijzen aan de nieuwe gebruiker
            ->add('tickets', EntityType::class, [
                'class' => Ticket::class,
                'multiple' => true,
                'by_reference' => false,
            ])
            ook moet ik in de addmethode zowel eigen property updaten als method op de owner aanroepen;
                        $this->tickets[] = $ticket;
                        $ticket->addUser($this);
            deze laatste geldt voor beide, owning en inverse, maar voor de owning staat het erpreciesal
            (if-checks in die methodes zijn nodige om geen infinite loops te creeren/infinite recursion)
X great tools for tinkering with annotations 
 php bin/console doctrine:schema:validate
 php bin/console doctrine:schema:update --force
- deleteing a users, deletes all related tickets and comments, but deleting a ticket or a comment doesnt delete user. Deleting a ticket, deletes a comment, but deleting a comment doesnt delete the ticket
X met cascade kan je removen en nog weel meer
  je kan dan zeggen wat de verschillende entities mogen doen bij anderen 
  hoe je het gebruikt is ook van belang voor de richting:
    beide richtingen moet je je annotations voor beide zo schrijven:
        /**
         * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="tickets", cascade={"remove"})
         */
    en dus ook
        /**
         * @ORM\ManyToMany(targetEntity="App\Entity\Ticket", mappedBy="users", cascade={"remove"})
         */
        private $tickets;
    waarbij ik doel op dat ze allemaal bij dezelfde cascade remove moeten hebben
    
    enkel 1 richting:
    1 zal dan 
    bij owner wat meer tekst zijn
             /**
              * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="tickets")
              * @ORM\JoinTable(name="ticket_user",
              *      joinColumns={@ORM\JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="CASCADE")},
              *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
              * )
              */
    en bij inverse dan 
        /**
         * @ORM\ManyToMany(targetEntity="App\Entity\Ticket", mappedBy="users", cascade={"remove"})
         */
        private $tickets;  
- user roles correspond to a series of authorizations. See security.yaml. Custom voter per object checks access
- note that I'm setting the author as a string, not as an object, when creating a new post. When checking access to one's own posts, strings, not objects are compared
- But I have a custom voter that doesn't care what's in the security.yaml file
    in the voter I have this 
            case 'POST_CREATE':
                // (dd($user));
                if ($this->security->isGranted('ROLE_CUSTOMER')) {
                    return true;
                }
    and this works fine even when I remove POST_CREATE from security.yaml
    ? am I mixing things up and using this role check where it shouldn't be used? 
    ? confusing because of how voters also watch for strings
    X voter checks for strings. POST_CREATE just activates my own voter instead of (immediatelly) activiating ROLE-system (which it does in a second step)
    X "Every role must start with ROLE_ (otherwise, things won't work as expected)" https://symfony.com/doc/current/security.html 
    X now I have:
    in security.yaml
      role_hierarchy:
          ROLE_ADMIN: [ROLE_POST_CREATE, ROLE_POST_VIEW, ROLE_ADMIN_TICKET] which gives ADMIN all these other roles as well
    in TicketController
        /**
         * @Route("/{id}/edit", name="ticket_edit", methods={"GET","POST"})
         */
        public function edit(Request $request, Ticket $ticket): Response
        {
            if (!$this->isGranted('TICKET_EDIT', $ticket)) {
                throw $this->createAccessDeniedException('No access!');
            }
    in TicketVoter        
            case 'TICKET_EDIT':
                if ($this->security->isGranted('ROLE_ADMIN_TICKET')) {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
    which gives the users with ROLE_ADMIN access to edit the ticket
-? what's the added value compared to just checking isGranted immediately in the router?
            if ($security->isGranted('ROLE_ADMIN_TICKET')) {
                throw $this->createAccessDeniedException('No access!');
            }
    x? the ability to add checks that don't fit the mould of ROLE or IS_AUTHENTICATED, but also e.g. 
            case 'TICKET_VIEW':
                if ($ticket->getAuthor() == $user->getEmail()) {
                    return true;
                }
- some form fields only visible dependening on ROLE. Some not rendered at all. Default values and such in the ...Type Form classes
            ->add('priorityLevel', null, [
                'empty_data' => '0',
            ])
            
            {% if is_granted('ROLE_ADMIN_TICKET') %}
                {{ form_row(form.priorityLevel) }}
                {{ form_row(form.externalStatusMessage) }}
                {{ form_row(form.users) }}
            {% endif %}
            {{ form_widget(form._token) }}
            <button class="btn">{{ button_label|default('Save') }}</button>
            {{ form_end(form, {'render_rest': false}) }}
            
## TO DO'S
- check routes. Need logical path for when not logged in and such and redirect to previous RELEVANT page. Think I'm also employing hard coded redirects sometimes and sometime that trait TargetPath or something
- need to selectively display properties: serializer ... but is'nt that for API? Will need to cha. edit: app.user in twig should do the trick
- clean this up and move lessons learned to notes
- dev  branch in master branch mergen0

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
