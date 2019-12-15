<?php

namespace App\Form;

use App\Entity\Simulation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimulationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias', TextType::class, [
                'required' => false
            ])
            ->add('category', TextType::class, [
                'required' => true
            ])
            ->add('token', TextType::class, [
                'required' => false
            ])
            ->add('http_verb', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'PUT' => 'PUT',
                    'DELETE' => 'DELETE',
                    'PATCH' => 'PATCH',
                    'HEAD' => 'HEAD',
                    'CONNECT' => 'CONNECT',
                    'OPTIONS' => 'OPTIONS',
                    'TRACE' => 'TRACE',
                ]
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
            ->add('response_code', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    '200' => 200,
                    '300' => 300,
                    '401' => 401,
                    '500' => 500,
                ]
            ])
            ->add('response_body_content', TextareaType::class, [
                'required' => false
            ])
            ->add('response_content_type', TextType::class, [
                'required' => false,
                'empty_data' => 'text',
            ])
            ->add('response_delay', TextType::class, [
                'required' => false,
                'empty_data' => 0,
            ])
            ->add('ttl', NumberType::class, [
                'required' => true,
                'empty_data' => 15,
                'label' => 'TTL (minutes)',
                'help' => 'Set 0 (zero) to keep forever.',
            ])
            ->add('active', CheckboxType::class, [
                'label'    => 'Simulation is active',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-save-simulation btn-primary'
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Simulation::class,
        ]);
    }
}
