<?php


class Company
{
    private $company; 
    public $vat; 

    public function setCompany($value)
    {
        $this->company = "test";
    }
    
    public function getCompany()
    {
        echo $this->company;
    }
}

$rc = new ReflectionClass(Company::class);
echo '<pre>'. print_r($rc->getProperties()); // all the proprierties
echo '<pre>'. print_r($rc->getProperties(ReflectionProperty::IS_PUBLIC)); // all the public proprierties
echo '<pre>'. print_r($rc->getMethods());// all the methods
echo '<pre>'. print_r($rc->getMethods(ReflectionMethod::IS_PUBLIC));// all public the methods

//esempio di come possiamo sfruttare questo methodo
foreach($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
    echo $method->getName();
    // echo $method->name; // stessa cosa di quello sopra
    echo '<pre>'.print_r($method->getParameters());// tutti i parametri che si aspettano le funzioni
}

echo '<pre>'. $rc->getName();

// risultato di tutti i metodi che posso usare:
echo '<pre>'. print_r(get_class_methods($rc));
// Array
// (
//     [0] => __construct
//     [1] => __toString
//     [2] => getName
//     [3] => isInternal
//     [4] => isUserDefined
//     [5] => isAnonymous
//     [6] => isInstantiable
//     [7] => isCloneable
//     [8] => getFileName
//     [9] => getStartLine
//     [10] => getEndLine
//     [11] => getDocComment
//     [12] => getConstructor
//     [13] => hasMethod
//     [14] => getMethod
//     [15] => getMethods
//     [16] => hasProperty
//     [17] => getProperty
//     [18] => getProperties
//     [19] => hasConstant
//     [20] => getConstants
//     [21] => getReflectionConstants
//     [22] => getConstant
//     [23] => getReflectionConstant
//     [24] => getInterfaces
//     [25] => getInterfaceNames
//     [26] => isInterface
//     [27] => getTraits
//     [28] => getTraitNames
//     [29] => getTraitAliases
//     [30] => isTrait
//     [31] => isAbstract
//     [32] => isFinal
//     [33] => getModifiers
//     [34] => isInstance
//     [35] => newInstance
//     [36] => newInstanceWithoutConstructor
//     [37] => newInstanceArgs
//     [38] => getParentClass
//     [39] => isSubclassOf
//     [40] => getStaticProperties
//     [41] => getStaticPropertyValue
//     [42] => setStaticPropertyValue
//     [43] => getDefaultProperties
//     [44] => isIterable
//     [45] => isIterateable
//     [46] => implementsInterface
//     [47] => getExtension
//     [48] => getExtensionName
//     [49] => inNamespace
//     [50] => getNamespaceName
//     [51] => getShortName
//     [52] => getAttributes
// )