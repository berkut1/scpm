<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Add;

use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private HostingPlanService $hostingPlanService;

    public function __construct(HostingPlanService $hostingPlanService)
    {
        $this->hostingPlanService = $hostingPlanService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $builder
            ->add('solidcp_id_plan', Type\ChoiceType::class,
                [
                    'label' => 'Plan',
                    'choices' => array_flip($this->hostingPlanService->allNotAddedHostingPlacesFrom($data->getIdHostingSpace())),
                    'required' => true,
                    //'placeholder' => 'Select a Plan'
                ])
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}