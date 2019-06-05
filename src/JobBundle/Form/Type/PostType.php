<?php
/**
 * Created by PhpStorm.
 * User: LAPTOP
 * Date: 12/3/2018
 * Time: 7:13 PM
 */

namespace JobBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('city')
            ->add('jobContract')
            ->add('companyName')
            ->add('companyAddress');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'JobBundle\Entity\Post',
            'csrf_protection' => false
        ]);
    }
}
