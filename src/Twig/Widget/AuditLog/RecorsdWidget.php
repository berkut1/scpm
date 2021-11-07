<?php
declare(strict_types=1);

namespace App\Twig\Widget\AuditLog;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RecorsdWidget extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('audit_log_records', [$this, 'records'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function records(Environment $twig, string $records): string
    {
        $records = json_decode($records, true);
        $formattedRecords = [];
        foreach ($records as $record){
            $values = [];
            foreach ($record['values'] as $value){
                $values[] = $value['value'];
            }
            unset($value);

            $translated = $this->translator->trans($record['text']);
            $formattedRecords[] = vsprintf($translated, $values);
//            try
//            {
//                $formattedRecords[] = vsprintf($translated, $values);
//            }catch (\ValueError $exception){
//                $formattedRecords[] = "$translated ValueError".$exception->getMessage();
//            }

        }
        unset($record);

        return $twig->render('widget/audit_log/records.html.twig', [
            'records' => $formattedRecords
        ]);
    }
}