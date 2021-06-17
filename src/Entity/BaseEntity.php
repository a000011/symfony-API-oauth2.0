<?php


namespace App\Entity;


use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseEntity
{
    /**This method serialize array
     * @param array $keys
     * @return String
     */
    public function serialize(array $keys): String
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer =  new Serializer($normalizers, $encoders);
        $serializedArray = $serializer->serialize(
            $this,
            'json',
            [AbstractNormalizer::ATTRIBUTES => $keys]
        );
        return $serializedArray;
    }


}