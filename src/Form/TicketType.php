<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de votre ticket',
                'attr' => ['placeholder' => 'Titre du ticket'],
                'required' => true,
            ])
            ->add('request', TextareaType::class, [
                'label' => 'Précisez votre requête',
                'attr' => ['placeholder' => 'description du ticket', 'cols' => '5', 'rows' => '5'],
                'required' => true,
            ])
            // ->add('isAttending', ChoiceType::class, [
            //     'choices'  => [
            //         'Maybe' => null,
            //         'Yes' => true,
            //         'No' => false,
            //     ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
