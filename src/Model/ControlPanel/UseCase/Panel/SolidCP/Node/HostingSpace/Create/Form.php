<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\Create;

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
            ->add('id_hosting_space', Type\ChoiceType::class,
                [
                    'label' => 'Hosting Space',
                    'choices' => array_flip($this->hostingSpaceService->allNotAddedHostingSpacesFrom($data->getIdEnterprise())),
                    'required' => true,
                    'placeholder' => 'Select a SolidCP Hosting Space'
                ])
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true
                ])->add('max_active_number', Type\IntegerType::class,
                [
                    'label' => 'Max Active Items',
                    'required' => true
                ])
            ->add('max_reserved_memory_mb', Type\IntegerType::class,
                [
                    'label' => 'Max Reserver RAM (MB)',
                    'required' => true
                ])
            ->add('space_quota_gb', Type\IntegerType::class,
                [
                    'label' => 'Space Quota (GB)',
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