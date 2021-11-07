<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Package\ChangePlans\SolidCP;

use App\ReadModel\ControlPanel\Panel\SolidCP\HostingSpace\HostingPlan\SolidcpHostingPlanFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private SolidcpHostingPlanFetcher $hostingPlanFetcher;

    public function __construct(SolidcpHostingPlanFetcher $hostingPlanFetcher)
    {
        $this->hostingPlanFetcher = $hostingPlanFetcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $builder
//            ->add('id_plan', Type\ChoiceType::class,
//                [
//                    'label' => 'Plan',
//                    'choices' => array_flip($this->hostingPlanFetcher->allNotAddedListForPackage($data->getIdPackage())),
//                    'required' => true,
//                    'placeholder' => 'Select a Plan'
//                ])
            ->add('id_plans', Type\ChoiceType::class,
                [
                    'label' => 'Plans',
                    'required' => false,
                    'multiple'  => true,
                    'expanded'  => true,
                    'choices' => array_flip($this->hostingPlanFetcher->allList()),
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}