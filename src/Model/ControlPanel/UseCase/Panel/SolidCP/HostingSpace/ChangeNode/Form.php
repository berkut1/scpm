<?php
declare(strict_types=1);

namespace App\Model\ControlPanel\UseCase\Panel\SolidCP\HostingSpace\ChangeNode;

use App\Model\ControlPanel\Service\SolidCP\HostingSpaceService;
use App\ReadModel\ControlPanel\Panel\SolidCP\EnterpriseServer\EnterpriseServerFetcher;
use App\ReadModel\ControlPanel\Panel\SolidCP\Node\SolidcpServerFetcher;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private EnterpriseServerFetcher $enterpriseServerFetcher;
    private SolidcpServerFetcher $serverFetcher;
    private HostingSpaceService $hostingSpaceService;

    public function __construct(EnterpriseServerFetcher $enterpriseServerFetcher, SolidcpServerFetcher $serverFetcher, HostingSpaceService $hostingSpaceService)
    {
        $this->enterpriseServerFetcher = $enterpriseServerFetcher;
        $this->serverFetcher = $serverFetcher;
        $this->hostingSpaceService = $hostingSpaceService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit')); //need to refill data to pass the form validation
    }

    protected function addElements(FormInterface $form, array $data, Command $modelOriginalData): void
    {
        $form
            ->add('id_enterprise', Type\ChoiceType::class,
                [
                    'label' => 'Enterprise server',
                    //'placeholder' => 'Select an Enterprise server',
                    'choices' => array_flip($this->enterpriseServerFetcher->allList()),
                    'required' => true,
                    //'data' => isset($data['id_enterprise']) ?? $data['id_enterprise'],
                ]);

        $servers = [];
        if (!empty($data['id_enterprise'])) {
            $servers = array_flip($this->serverFetcher->allListFrom((int)$data['id_enterprise']));
        }
        $form->add('id_server', Type\ChoiceType::class,
            [
                'label' => 'Node',
                'required' => true,
                //'placeholder' => 'Select an Enterprise first...',
                'choices' => $servers,
                //'data' => isset($data['id_server']) ?? $data['id_server'],
            ]);
//
//        $spaces = [];
//        if (!empty($data['id_server'])) {
//            $spaces = array_flip($this->hostingSpaceService->allNotAddedHostingSpacesExceptHostingSpaceIdFrom((int)$data['id_enterprise'], $modelOriginalData->id_solidcp_hosting_space));
//        }
//        $form->add('id_solidcp_hosting_space', Type\ChoiceType::class,
//            [
//                'label' => 'Hosting Space',
//                'required' => true,
//                //'placeholder' => 'Select a Node first...',
//                'choices' => $spaces,
//                //'data' => isset($data['id_hosting_space']) ?? $data['id_hosting_space'],
//            ]);
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}