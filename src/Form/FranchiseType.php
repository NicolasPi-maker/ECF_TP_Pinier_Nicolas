<?php

namespace App\Form;

use App\Entity\Franchise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class FranchiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user_id')
            ->add('client_name')
            ->add('client_address')
            ->add('url', null, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '^[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)^',
                        'message' => 'L\'url n\'est pas une url valide'
                    ])
                ],
            ])
            ->add('logo_url', FileType::class, [
              'label' => false,
              'data_class' => null,
              'required' => false,
              'mapped' => false,
            ])
            ->add('technical_contact', EmailType::class)
            ->add('commercial_contact', EmailType::class)
            ->add('short_description', TextType::class, [
              'attr' => [
                'maxlength' => 80,
                'minlength' => 60,
            ],
            ])
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
