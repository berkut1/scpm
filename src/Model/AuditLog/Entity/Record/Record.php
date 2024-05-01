<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity\Record;

final class Record implements \JsonSerializable
{
    private string $text;
    /** @var $values Value[] */
    private array $values;

    public static function create(string $text, array $values): self
    {
        $log = new self();
        $arr = [];
        foreach ($values as $value) {
            if ($value instanceof \DateTimeImmutable) {
                $arr[] = new Value($value->format('Y-m-d'));
            } else {
                $arr[] = new Value((string)$value); //everything to string
            }
        }
        $log->text = $text;
        $log->values = $arr;

        return $log;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /** @return Value[] */
    public function getValues(): array
    {
        return $this->values;
    }

    public static function setFromDecodedJSON(array $data): self
    {
        $log = new self();
        foreach ($data as $key => $val) {
            if (property_exists(self::class, $key)) {
                if (is_array($val)) { //as array, we can get only Values there, so not need to check something else
                    $arr = [];
                    foreach ($val as $one) {
                        $arr[] = Value::setFromDecodedJSON($one);
                    }
                    $val = $arr;
                    //$val = Value::setFromDecodedJSON($val);
                }
                $log->$key = $val;
            }
        }
        return $log;
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}