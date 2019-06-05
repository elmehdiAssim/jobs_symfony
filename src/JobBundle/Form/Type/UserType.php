<?php
/**
 * Created by PhpStorm.
 * User: LAPTOP
 * Date: 12/4/2018
 * Time: 7:56 PM
 */

namespace JobBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName')
            ->add('email');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'JobBundle\Entity\User',
            'csrf_protection' => false
        ]);
    }
}