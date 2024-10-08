<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\OsTemplate\Add;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $builder
            ->add('osTemplates', CollectionType::class, [
                'label' => false,
                'entry_type' => Collection\Form::class,
                'entry_options' => [
                    'label' => false,
                    'id_enterprise_dispatcher' => $data->id_enterprise_dispatcher,
                    'packageId' => $data->packageId,
                ],
                'by_reference' => false,
                // this allows the creation of new forms and the prototype too
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}