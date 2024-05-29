<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\EnterpriseDispatcher\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ])
            ->add('url', Type\TextType::class,
                [
                    'label' => 'Url',
                    'required' => true,
                ])
            ->add('login', Type\TextType::class,
                [
                    'label' => 'Login',
                    'required' => true,
                ])
            ->add('password', Type\PasswordType::class,
                [
                    'label' => 'Password',
                    'always_empty' => false,
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}