<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CommentTypeEmbeddedForm extends AbstractType
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
            ->add('commentText', TextType::class)
            ->add('isCommentPublic')
<<<<<<< HEAD:src/Form/CommentTypeReopenmbeddedForm.php
            ->add('author', EmailType::class,
                [
=======
//            ->add('createdAt')
//            ->add('updatedAt')
            ->add('author', EmailType::class, [
>>>>>>> parent of f8cc6b3... you can add a comment on reopening ticket. But form looks like a mess. This, however, is intentional, as not rendering fields may results in dramatic errors in database:src/Form/CommentTypeEmbeddedForm.php
                'empty_data' => $this->security->getUser()->getUsername(),
            ]);
        $builder
            ->get('author')
            ->addModelTransformer($this->modelTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
