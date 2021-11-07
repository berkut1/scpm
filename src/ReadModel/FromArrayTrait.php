<?php
declare(strict_types=1);

namespace App\ReadModel;

trait FromArrayTrait //https://stackoverflow.com/questions/64468289/how-to-fetch-results-into-a-custom-object-now-that-fetchall-and-fetchmode-are
{
    public static function fromArray(array $data = []): self
    {
        foreach (get_object_vars($obj = new self) as $property => $default) {
            if($default === null){ //TODO: use reflection like there ??? https://stackoverflow.com/questions/59189498/get-type-of-typed-property-in-php-7-4
                throw new \UnexpectedValueException('Oops in your class is a null property - ' . self::class);
            }
            $type = gettype($default);
            $var = $data[$property] ?? $default;
            settype($var, $type);
            $obj->$property = $var;
        }
        return $obj;
    }
}