<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                ])
            ->add('max_active_number', Type\IntegerType::class,
                [
                    'label' => 'Max Active Items',
                    'required' => true,
                ])
            ->add('max_reserved_memory_mb', Type\IntegerType::class,
                [
                    'label' => 'Max Reserver RAM (MB)',
                    'required' => true,
                ])
            ->add('space_quota_gb', Type\IntegerType::class,
                [
                    'label' => 'Space Quota (GB)',
                    'required' => true,
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}