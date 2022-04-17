<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
                'required' => true,
            ])
            // ->add('adminLinkToken', PasswordType::class, [
            //     'label' => 'Mot de passe.',
            //     'attr' => ['placeholder' => 'Conservez ce petit token précieusement.'],
            //     'required' => true,
            // ])
            ->add('email', EmailType::class, [
                'label' => 'Précisez votre Email.',
                'attr' => ['Précisez votre Email.'],
                'required' => true,
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
