<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Model\User;
use AppBundle\Model\Group;
use AppBundle\Repository\GroupRepository;

class UserFormType extends AbstractType  {


    public function buildForm(FormBuilderInterface $builder, array $options) {
         $builder
            ->add('username',  null)
            ->add('email', EmailType::class)
            ->add('enabled', ChoiceType::class, array(
                'choices' => array(
                    'Yes' => true,
                    'No' => false,
                ),
                'expanded' => true,
                'label_attr' => array(
                 'class' => 'radio-inline'
             )
            ))
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password'
                ],
                'second_options' => [
                    'label' => 'Confirm Password'
                ]
            ])
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'query_builder' => function (GroupRepository $er) {
                    return $er->createQueryBuilder('x')->orderBy('x.name', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
                'label_attr' => array(
                'class' => 'checkbox-inline'
             )
            ])
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-default')));
    }

    public function getParent(){
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }


    public function getBlockPrefix(){
        return 'app_user_registration';
    }

}