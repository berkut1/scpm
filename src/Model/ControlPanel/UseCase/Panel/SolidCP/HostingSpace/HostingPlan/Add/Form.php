<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\HostingPlan\Add;

use App\Event\FormErrorEvent;
use App\Model\ControlPanel\Service\SolidCP\HostingPlanService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class Form extends AbstractType
{
    public function __construct(
        private readonly HostingPlanService       $hostingPlanService,
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $choices = [];

        try {
            $choices = array_flip($this->hostingPlanService->allNotAddedHostingPlanesFrom($data->getIdHostingSpace()));
        } catch (\Exception $e) {
            $this->dispatcher->dispatch(new FormErrorEvent($e, 'Error fetching hosting planes'));
        }

        $builder
            ->add('solidcp_id_plan', Type\ChoiceType::class,
                [
                    'label' => 'Plan',
                    'choices' => $choices,
                    'required' => true,
                    //'placeholder' => 'Select a Plan'
                ])
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}