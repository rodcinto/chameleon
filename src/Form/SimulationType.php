<?php

namespace App\Form;

use App\Entity\Simulation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', TextType::class, [
                'required' => true
            ])
            ->add('token', TextType::class, [
                'required' => false
            ])
            ->add('http_verb', TextType::class, [
                'required' => true
            ])
            ->add('parameters', TextareaType::class, [
                'required' => false
            ])
            ->add('query_string', TextType::class, [
                'required' => false
            ])
            ->add('request_body_content', TextareaType::class, [
                'required' => false
            ])
            ->add('response_code', TextType::class, [
                'required' => false
            ])
            ->add('response_body_content', TextareaType::class, [
                'required' => false
            ])
            ->add('response_content_type', TextType::class, [
                'required' => false
            ])
            ->add('response_delay', TextType::class, [
                'required' => false
            ])
            ->add('ttl', NumberType::class, [
                'required' => true,
            ])
            ->add('active', CheckboxType::class, [
                'label'    => 'Show this entry publicly?',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-save-simulation'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Simulation::class,
        ]);
    }
}
