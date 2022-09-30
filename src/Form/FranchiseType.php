<?php

namespace App\Form;

use App\Entity\Franchise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FranchiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user_id')
            ->add('client_name')
            ->add('client_address')
            ->add('url')
            ->add('logo_url', FileType::class, array('data_class' => null))
            ->add('technical_contact', EmailType::class)
            ->add('commercial_contact', EmailType::class)
            ->add('short_description', TextType::class)
            ->add('full_description', TextareaType::class)
            ->add('is_active')
            ->add('sell_drink')
            ->add('manage_schedule')
            ->add('create_newsletter')
            ->add('add_promotion')
            ->add('sell_food')
            ->add('create_event')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Franchise::class,
        ]);
    }
}
