<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CommentType extends AbstractType
{
//    private $referer;
//
//    public function __construct(Request $request)
//    {;
//        $this->referer = $request->headers->get('referer');
//    }
//    private $security;
//
//    public function __construct(Security $security)
//    {
//        $this->security = $security;
//        // dd($security);
//    }

//    private $referer;
//
//    public function __construct(RequestStack $requestStack)
//    {
//        $this->referer = $requestStack->getCurrentRequest()->headers->get('referer');
//        // dd($this->referer);
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
//            ->add('referer', HiddenType::class, [
//                'data' => $this->referer,
//                    ])

            ->add('user');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
