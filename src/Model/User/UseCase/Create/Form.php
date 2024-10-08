<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\User\Entity\User\Role;
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
            ->add('login', Type\TextType::class, ['label' => 'Login'])
            ->add('password', Type\PasswordType::class, ['label' => 'Password'])
            ->add('role', Type\ChoiceType::class, ['choices' => Role::getArray(), 'required' => true]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}
