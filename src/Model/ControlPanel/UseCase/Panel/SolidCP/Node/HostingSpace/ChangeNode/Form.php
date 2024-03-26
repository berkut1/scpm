<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;

use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(private readonly SolidcpServerFetcher $serverFetcher) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $data */
        $data = $options['data'];
        $builder
            ->add('id_server', Type\ChoiceType::class,
                [
                    'label' => 'Node/Server',
                    'choices' => array_flip($this->serverFetcher->allListFrom($data->getIdEnterpriseDispatcher())),
                    'required' => true,
                    'placeholder' => 'Move to Node',
                ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}