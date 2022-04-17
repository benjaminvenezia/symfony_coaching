<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TokenLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adminLinkToken', PasswordType::class, [
                'label' => 'accédez à votre ressource en utilisant votre token..',
                'attr' => ['placeholder' => 'Tk5a3CnUsWKJM73bq9B9_PEvV_D1UaxLlp'],
                'required' => true,
            ])
        ;
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Event::class,
    //     ]);
    // }
}
