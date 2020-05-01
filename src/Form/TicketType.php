<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('priorityLevel', IntegerType::class, [
                'empty_data' => '0',
            ])
            ->add('externalStatusMessage', TextType::class, [
                'empty_data' => 'open',
            ])
            ->add('ticketText', TextType::class, [
                    'empty_data' => 'testemptydatadefault',
                ]
            )
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('author', EmailType::class, [
                'empty_data' => $this->security->getUser()->getUsername(),
            ])
//            ->add('comments', EntityType::class, [
//                'class' => Comment::class,
//                'multiple' => true,
//                'by_reference' => false,
//            ])
            ->add(
                'comments',
                CollectionType::class,
                array(
                    'entry_type' => CommentTypeEmbeddedForm::class,
                    'entry_options' => ['label' => false],
//                    'label' => 'Support Entries',
//                    'error_bubbling' => true,
                    'allow_add' => true,
                    'by_reference' => false,
//                    'cascade_validation' => true,
                )
            );

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
