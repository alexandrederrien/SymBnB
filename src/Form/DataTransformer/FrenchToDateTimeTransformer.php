<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    private $frenchFormat = 'd/m/Y';

    public function transform($date)
    {
        if(is_null($date)) {
            return '';
        }

        return $date->format($this->frenchFormat);
    }

    public function reverseTransform($frenchDate)
    {
        if(is_null($frenchDate)) {
            //Exception
            throw new TransformationFailedException("Vous devez fournir une date");
        }

        $date = \DateTime::createFromFormat($this->frenchFormat, $frenchDate);

        if($date === false) {
            //Exception
            throw new TransformationFailedException("Le format de la date n'est pas le bon. Le bon format est ".$this->frenchFormat);
        }

        return $date;
    }
}