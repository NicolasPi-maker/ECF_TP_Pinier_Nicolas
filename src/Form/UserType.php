<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
              'attr' => ['autocomplete' => 'new-password'],
              'constraints' => [
                new NotBlank([
                  'message' => 'Veuillez saisir un mot de passe',
                ]),
                new Regex([
                  'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$^',
                  'message' => 'Ce mot de passe n\'est pas assez fort il doit contenir :'
                ])
              ],
            ])
            ->add('roles', ChoiceType::class, [
              'choices' => [
                'Franchise' =>
                  "ROLE_FRANCHISE",

                'Structure' =>
                  "ROLE_STRUCTURE",

              ],
              'required' => true,
              'placeholder' => 'Choisissez un rôle'
            ]);

      // Data transformer
      $builder->get('roles')
        ->addModelTransformer(new CallbackTransformer(
          function ($rolesArray) {
            // transform the array to a string
            return count($rolesArray)? $rolesArray[0]: null;
          },
          function ($rolesString) {
            // transform the string back to an array
            return [$rolesString];
          }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
