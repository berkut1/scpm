<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\ChangePlans\SolidCP;

use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(private readonly SolidcpHostingPlanFetcher $hostingPlanFetcher) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_plans', Type\ChoiceType::class,
                [
                    'label' => 'Plans',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array_flip($this->hostingPlanFetcher->allList()),
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}