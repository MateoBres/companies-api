<?php

// per vedere gli errori
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Serializer
{
    // serialize($data, $type)
    //      normalize ($data)
    //      encode -> json

    //deserialization
    //      decode($data)
    //      denormalize($data, $type)

    public function serialize($data, string $type = "Object")
    {
        switch($type) {
            case "Object":
                $data = $this->objectNormalizer($data);
                break;
            case "GetSetMethod":
                break;
            default:
                throw new \Exception($type." not found");
        }

        return $this->encode($data);
    }

    public function encode($data)
    {
        try{
            return json_encode($data);
        } catch (Exception $e) {
            throw $e;
        }
        
    }

    public function objectNormalizer($data)
    {
        $finalData = [];
        // check if we have an array
        if(is_iterable($data)){
            foreach($data as $item) {
                $finalData[] = $this->normalizeSingleObject($item);
            }
        } else {
            $finalData = $this->normalizeSingleObject($data);
        }
        // return single data
        return $finalData;
    }

    // normalize using public proprierties
    public function normalizeSingleObject($data): array
    {
        $array = [];
        // check if we have an object
        if (is_object($data)){
            $reflectionClass = new ReflectionClass($data);
            $propierties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($propierties as $property) {
                $array[$property->getName()] = $property->getValue($data);
            }
        }

        return $array;
    }
}

class Company
{
    public $company; 
    public $vat; 

    public function getCompany() 
    {
        return $this->company;
    }

    public function setCompany($value): void // void perche' non ritorna niente
    {
        $this->company = $value;

    }  
    
    public function getVat() 
    {
        return $this->vat;
    }

    public function setVat($value): void // void perche' non ritorna niente
    {
        $this->vat = $value;

    }  
}

$companyArray = [];

$company1 = new Company();
$company1->setCompany("my company");
$company1->setVat("11111111111");
$companyArray[] = $company1;

$company2 = new Company();
$company2->setCompany("my company");
$company2->setVat("22222222222");
$companyArray[] = $company2;

$company3 = new Company();
$company3->setCompany("my company");
$company3->setVat("33333333333");
$companyArray[] = $company3;

$company4 = new Company();
$company4->setCompany("my company");
$company4->setVat("44444444444");
$companyArray[] = $company4;
// var_dump($companyArray);die();
$serializer = new Serializer();
$result = $serializer->serialize($companyArray, 'Object');
echo '<pre>'.print_r($result);
// var_dump($result);
