<?php

namespace App\Form;

use App\Entity\Structure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class StructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('structure_name')
            ->add('structure_address')
            ->add('manager_name')
            ->add('user_id')
            ->add('logo_url', FileType::class, [
              'label' => false,
              'data_class' => null,
              'required' => false,
              'mapped' => false,
            ])
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
            ->add('is_active')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Structure::class,
        ]);
    }
}
