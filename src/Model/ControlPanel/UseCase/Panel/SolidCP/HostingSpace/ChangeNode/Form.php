<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeNode;

use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseDispatcher\EnterpriseDispatcherFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Form extends AbstractType
{
    public function __construct(
        private readonly EnterpriseDispatcherFetcher $enterpriseDispatcherFetcher,
        private readonly SolidcpServerFetcher        $serverFetcher,
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, $this->onPreSetData(...));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $this->onPreSubmit(...)); //need to refill data to pass the form validation
    }

    protected function addElements(FormInterface $form, array $data, Command $modelOriginalData): void
    {
        $form
            ->add('id_enterprise_dispatcher', Type\ChoiceType::class,
                [
                    'label' => 'Enterprise server',
                    //'placeholder' => 'Select an Enterprise server',
                    'choices' => array_flip($this->enterpriseDispatcherFetcher->allList()),
                    'required' => true,
                    //'data' => isset($data['id_enterprise_dispatcher']) ?? $data['id_enterprise_dispatcher'],
                ]);

        $servers = [];
        if (!empty($data['id_enterprise_dispatcher'])) {
            $servers = array_flip($this->serverFetcher->allListFrom((int)$data['id_enterprise_dispatcher']));
        }
        $form->add('id_server', Type\ChoiceType::class,
            [
                'label' => 'Node',
                'required' => true,
                //'placeholder' => 'Select an Enterprise first...',
                'choices' => $servers,
                //'data' => isset($data['id_server']) ?? $data['id_server'],
            ]);
    }

    function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData(); //array
        $modelOriginalData = $event->getForm()->getData(); //object

        $this->addElements($form, $data, $modelOriginalData);
    }

    function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $modelOriginalData = $event->getData(); //object
        $data = json_decode(json_encode($modelOriginalData), true); //convert object to assoc array

        $this->addElements($form, $data, $modelOriginalData);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Command::class]);
    }
}