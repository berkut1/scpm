<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\User\Create;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(private readonly EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_enterprise_dispatcher', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->enterpriseDispatcherFetcher->allList()),
                    'placeholder' => 'Use Default',
                    'required' => false,
                ])
            ->add('username', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ])
            ->add('firstName', Type\TextType::class,
                [
                    'label' => 'First Name',
                    'required' => false,
                ])
            ->add('lastName', Type\TextType::class,
                [
                    'label' => 'Last Name',
                    'required' => false,
                ])
            ->add('email', Type\EmailType::class,
                [
                    'label' => 'email',
                    'required' => true,
                ])
            ->add('password', Type\PasswordType::class,
                [
                    'label' => 'Password',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}