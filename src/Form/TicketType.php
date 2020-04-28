<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TicketType extends AbstractType
{
    public function __construct(Security $security)
    {
        $this->security = $security;
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
            ->add('author', null, [
                'empty_data' => $this->security->getUser(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
