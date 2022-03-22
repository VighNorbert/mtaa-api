<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Repository\ValidationSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{

    /**
     * @param array $parameters
     * @param ValidationSchema[] $validationSchemas
     * @return array
     * @throws ValidationException
     */
    public function parametersValidation(array $parameters = [], array $validationSchemas = []): array
    {
        foreach ($parameters as $key => $parameter) {
            if (isset($validationSchemas[$key]) && $schema = $validationSchemas[$key]) {
                if (
                    (is_array($schema->getAllowedValues()) && !in_array($parameter, $schema->getAllowedValues()))
                    || ($schema->getAllowedValues() == ValidationSchema::VALIDATE_NUMBER && !$this->is_number(strval($parameter)))
                    || ($schema->getAllowedValues() == ValidationSchema::VALIDATE_NUMBER_GTZ && !$this->is_gtz_number(strval($parameter)))
                    || ($schema->getAllowedValues() == ValidationSchema::VALIDATE_EMAIL && !$this->is_email(strval($parameter)))
                    || ($schema->getAllowedValues() == ValidationSchema::VALIDATE_PHONE && !$this->is_phone(strval($parameter)))
                    || ($schema->getAllowedValues() == ValidationSchema::VALIDATE_TIME && !$this->is_time(strval($parameter)))
                ) {
                    if ($schema->getDefaultValue() == null) {
                        throw new ValidationException(sprintf('Parameter \'%s\' nemá požadovaný formát.', $key));
                    }
                    else {
                        $parameters[$key] = $schema->getDefaultValue();
                    }
                }
                if (is_string($parameter)) {
                    if ($schema->getMax() !== null && strlen($parameter) > $schema->getMax()) {
                        throw new ValidationException(sprintf('Parameter \'%s\' musí mať maximálnu dĺžku %d znakov.', $key, $schema->getMax()));
                    }
                    if ($schema->getMin() !== null && strlen($parameter) < $schema->getMin()) {
                        throw new ValidationException(sprintf('Parameter \'%s\' musí mať minimálnu dĺžku %d znakov.', $key, $schema->getMin()));
                    }
                } elseif (is_int($parameter)) {
                    if ($schema->getMax() !== null && $parameter > $schema->getMax()) {
                        throw new ValidationException(sprintf('Parameter \'%s\' musí mať maximálnu hodnotu %d.', $key, $schema->getMax()));
                    }
                    if ($schema->getMin() !== null && $parameter < $schema->getMin()) {
                        throw new ValidationException(sprintf('Parameter \'%s\' musí mať maximálnu hodnotu %d.', $key, $schema->getMin()));
                    }
                }
            }
        }
        return $parameters;
    }

    public function is_number(string $parameter): bool
    {
        return preg_match('/^[0-9]+$/', $parameter);
    }

    public function is_gtz_number(string $parameter): bool
    {
        return preg_match('/^[1-9][0-9]*$/', $parameter);
    }

    public function is_email(string $parameter): bool
    {
        return filter_var($parameter, FILTER_VALIDATE_EMAIL);
    }

    public function is_phone(string $parameter): bool
    {
        return preg_match('/^(\+420|\+421|0)( ?[0-9]{3}){3}$/', $parameter);
    }

    public function is_time(string $parameter): bool
    {
        return preg_match('/^([0-1][0-9]|2[0-3])(:[0-5][0-9]){1,2}$/', $parameter);
    }

}