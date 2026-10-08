<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Interface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @api
 */
class RegistrationFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => new TranslatableMessage(
                    'email',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email(null, 'invalidEmail'),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => new TranslatableMessage(
                    'errorPasswordsDifferent',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'options' => [
                    'attr' => [
                        'class' => 'password-field', 'autocomplete' => 'new-password',
                    ],
                ],
                'required' => true,
                'first_options' => [
                    'label' => new TranslatableMessage(
                        'password',
                        [],
                        'PhpAuthCoreBundle'
                    ),
                    'constraints' => [
                        new NotBlank(),
                        new Length(null, 8, 200),
                    ],
                ],
                'second_options' => ['label' => new TranslatableMessage(
                    'reenterPassword',
                    [],
                    'PhpAuthCoreBundle'
                )],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => $options['agreeTerms_label'],
                'label_html' => true,
                'translation_domain' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue(
                        null,
                        new TranslatableMessage(
                            'mustAgreeTerms',
                            [],
                            'PhpAuthCoreBundle'
                        )->getMessage(),
                    ),
                ],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // ...,
            'agreeTerms_label' => '',
        ]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('agreeTerms_label', 'string');
    }
}
