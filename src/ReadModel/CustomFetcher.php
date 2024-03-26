<?php
declare(strict_types=1);

namespace App\ReadModel;

use Doctrine\DBAL\Result;

final class CustomFetcher
{
    /**
     * @return CustomObjectInterface[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchCustomObjectArray(Result $statement, CustomObjectInterface $customObjectClass): array
    {
        $arr = [];
        foreach ($statement->fetchAllAssociative() as $row) {
            $arr[] = $customObjectClass::fromArray($row);
        }
        return $arr;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchCustomObject(Result $statement, CustomObjectInterface $customObjectClass): ?CustomObjectInterface
    {
        foreach ($statement->fetchAllAssociative() as $row) {
            /** @var CustomObjectInterface $result */
            $result = $customObjectClass::fromArray($row);
            return $result;
        }
        return null;
    }
}