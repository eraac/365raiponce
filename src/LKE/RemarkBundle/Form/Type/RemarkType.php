<?php

namespace LKE\RemarkBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemarkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('context', 'text')
            ->add('sentence', 'textarea')
            ->add('email', 'email')
            ->add('theme', 'entity', array(
                'class' => 'LKERemarkBundle:Theme',
                'choice_label' => 'name'
            ))
            ->add('emotion', 'entity', array(
                'class' => 'LKERemarkBundle:Emotion',
                'choice_label' => 'name'
            ))
            ->add('scaleEmotion', 'number')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LKE\RemarkBundle\Entity\Remark'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}