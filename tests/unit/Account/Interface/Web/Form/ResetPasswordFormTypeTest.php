<?php

declare(strict_types=1);

namespace Xver\PhpAuthCoreBundle\Tests\unit\Account\Interface\Web\Form;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Xver\PhpAuthCoreBundle\Account\Interface\Web\Form\ResetPasswordFormType;

/**
 * @internal
 */
#[CoversClass(ResetPasswordFormType::class)]
#[AllowMockObjectsWithoutExpectations]
class ResetPasswordFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'newPassword' => ['first' => 'password', 'second' => 'password'],
        ];
        $expectedOutputFormData = $formData;

        $form = $this->factory->create(ResetPasswordFormType::class, $formData);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $model was modified as expected when the form was submitted
        $this->assertEquals($expectedOutputFormData, $formData);
    }

    public function testCustomFormView()
    {
        // ... prepare the data as you need
        $formData = [
            'newPassword' => 'password',
        ];

        // The initial data may be used to compute custom view variables
        $view = $this->factory->create(ResetPasswordFormType::class, $formData)
            ->createView()
        ;

        $this->assertArrayHasKey('value', $view->vars);
        $this->assertSame($formData, $view->vars['value']);
    }

    protected function getExtensions(): array
    {
        $validator = $this->createStub(ValidatorInterface::class);
        $metadata = $this->getMockBuilder(ClassMetadata::class)
            ->setConstructorArgs([''])
            ->onlyMethods(['addPropertyConstraint'])
            ->getMock()
        ;

        $validator->method('getMetadataFor')->willReturn($metadata);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        return [new ValidatorExtension($validator)];
    }
}
