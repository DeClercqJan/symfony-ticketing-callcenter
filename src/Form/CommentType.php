<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
//    private $ticket;
//
//    public function __construct(Ticket $ticket)
//    {
//        $this->ticket = $ticket;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commentText')
            ->add('isCommentPublic')
            ->add('createdAt')
            ->add('updatedAt')
//            ->add('ticket', EntityType::class, [
//                'class' => Ticket::class,
////                'empty_data' => $this->ticket,
//            ])
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
