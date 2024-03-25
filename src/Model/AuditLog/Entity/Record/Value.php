<?php
declare(strict_types=1);

namespace App\Model\AuditLog\Entity\Record;


final class Value implements \JsonSerializable
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function setFromDecodedJSON(array $data): self
    {
        if (!isset($data['value'])) {
            throw new \DomainException('Json array must have the property - value');
        }
        return new self($data['value']);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}