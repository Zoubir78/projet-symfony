<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le champs nom est manquant.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut contenir plus de {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('place', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le champ lieu est manquant.'])
                ]
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Date de création manquante.']),
                    new Range([
                        'max' => new \DateTime(),
                        'maxMessage' => 'La date ne peut être future.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
