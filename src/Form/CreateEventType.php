<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'événement.',
                'attr' => ['placeholder' => 'Nom de l\'événement.'],
                'required' => false,
            ])
            ->add('adminLinkToken', TextType::class, [
                'label' => 'token.',
                'attr' => ['placeholder' => 'Nom de l\'événement.'],
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Précisez votre Email.',
                'attr' => ['Précisez votre Email.'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
