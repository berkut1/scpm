<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\Node\HostingSpace\ChangeNode;

use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private SolidcpServerFetcher $serverFetcher;

    public function __construct(SolidcpServerFetcher $serverFetcher)
    {
        $this->serverFetcher = $serverFetcher;
    }

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
                    'placeholder' => 'Move to Node'
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}