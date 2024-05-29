<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\SOAP\Package\Create;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(
        private readonly EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher,
        private readonly SolidcpHostingPlanFetcher   $solidcpHostingPlanFetcher
    ) {}

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
            ->add('planId', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->solidcpHostingPlanFetcher->allListWithSolidCpIdPlan()),
                    'required' => true,
                ])
            ->add('userId', Type\IntegerType::class,
                [
                    'label' => 'UserID',
                    'required' => true,
                ])
            ->add('spaceName', Type\TextType::class,
                [
                    'label' => 'Space Name',
                    'required' => false,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}