<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TicketType extends AbstractType
{
    private $security;

    private $modelTransformer;

    public function __construct(Security $security, EmailToUserTransformer $EmailToUserTransformer)
    {
        $this->security = $security;
        $this->modelTransformer = $EmailToUserTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // default values added
        $builder
            ->add('priorityLevel', null, [
                'empty_data' => '0',
            ])
            ->add('externalStatusMessage', null, [
                'empty_data' => 'open',
            ])
            ->add('ticketText')
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('users')
            ->add('author', EmailType::class, [
                'empty_data' => $this->security->getUser()->getUsername(),
            ]);

        $builder
            ->get('author')
            ->addModelTransformer($this->modelTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
