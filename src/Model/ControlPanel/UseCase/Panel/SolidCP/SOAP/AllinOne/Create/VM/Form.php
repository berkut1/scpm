<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\AllinOne\Create\VM;

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
                    'placeholder' => 'Default',
                    'required' => false,
                ])
            ->add('client_login', Type\TextType::class,
                [
                    'label' => 'Client Name',
                    'required' => true,
                ])
            ->add('client_email', Type\EmailType::class,
                [
                    'label' => 'Client Email',
                    'required' => true,
                ])
            ->add('client_password', Type\PasswordType::class,
                [
                    'label' => 'Client Password',
                    'required' => true,
                ])
            ->add('server_package_name', Type\TextType::class,
                [
                    'label' => 'Server Package',
                    'required' => true,
                ])
            ->add('server_location_name', Type\TextType::class,
                [
                    'label' => 'Server Location',
                    'required' => true,
                ])
            ->add('server_os_name', Type\TextType::class,
                [
                    'label' => 'Server OS',
                    'required' => true,
                ])
            ->add('server_password', Type\PasswordType::class,
                [
                    'label' => 'Server Password',
                    'required' => true,
                ])
            ->add('server_ip_amount', Type\IntegerType::class,
                [
                    'label' => 'Amount of IPs',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}