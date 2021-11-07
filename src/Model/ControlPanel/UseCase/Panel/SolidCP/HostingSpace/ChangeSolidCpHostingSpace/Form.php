<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeSolidCpHostingSpace;

use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private HostingSpaceService $hostingSpaceService;

    public function __construct(HostingSpaceService $hostingSpaceService)
    {
        $this->hostingSpaceService = $hostingSpaceService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $builder
            ->add('id_solidcp_hosting_space', Type\ChoiceType::class,
            [
                'label' => 'Hosting Space',
                'required' => true,
                'choices' => array_flip($this->hostingSpaceService->allNotAddedHostingSpacesExceptHostingSpaceIdFrom($data->id_enterprise, $data->id_solidcp_hosting_space)),
                //'data' => isset($data['id_hosting_space']) ?? $data['id_hosting_space'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}