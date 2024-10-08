<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\Edit;

use App\ReadModel\ControlPanel\Location\LocationFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(
        private readonly EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher,
        private readonly LocationFetcher             $locationFetcher
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_enterprise_dispatcher', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->enterpriseDispatcherFetcher->allList()),
                    'required' => true,
                    //'placeholder' => 'All Locations'
                ])
            ->add('id_location', Type\ChoiceType::class,
                [
                    'choices' => array_flip($this->locationFetcher->allList()),
                    'required' => true,
                    'placeholder' => 'Select a Location',
                ])
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ])
            ->add('cores', Type\IntegerType::class,
                [
                    'label' => 'Cores',
                    'required' => true,
                ])
            ->add('threads', Type\IntegerType::class,
                [
                    'label' => 'Threads',
                    'required' => true,
                ])
            ->add('ram_mb', Type\IntegerType::class,
                [
                    'label' => 'RAM (MB)',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}