<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Account\Interface\Web\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @api
 */
class ChangePasswordFormType extends ResetPasswordFormType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', null, [
                'label' => new TranslatableMessage(
                    'email',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'required' => true,
                'mapped' => false,
                'attr' => ['style' => 'display:none;'],
                'label_attr' => ['style' => 'display:none;'],
            ])
            ->add('currentPassword', PasswordType::class, [
                'invalid_message' => new TranslatableMessage(
                    'errorPasswordsDifferent',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'required' => true,
                'mapped' => false,
                'label' => new TranslatableMessage(
                    'currentPassword',
                    [],
                    'PhpAuthCoreBundle'
                ),
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(),
                    new Length(null, 8, 200),
                ],
            ])
        ;
        parent::buildForm($builder, $options);
    }
}
