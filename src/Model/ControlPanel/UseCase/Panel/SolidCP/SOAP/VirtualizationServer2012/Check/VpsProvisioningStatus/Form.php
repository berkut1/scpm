<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\VirtualizationServer2012\Check\VpsProvisioningStatus;

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
            ->add('solidcp_item_id', Type\IntegerType::class,
                [
                    'label' => 'itemId',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}