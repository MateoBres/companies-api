<?php

// CLASSE FAKE SBAGLIATA SENZA INTERFACCIA
// class MailGunService
//{
//     public function sendEmail($body)
//     {
//         return null;
//     }
// }

// CLASSE FAKE CORRETTA IMPLEMENTANDO INTERFACCIA
class MailGunService implements EmailServiceInterface
{
    public function sendEmail($body)
    {
        return null;
    }
}

// INTERFACCIA
interface EmailServiceInterface
{
    function sendEmail($body);
}

// MODO SBAGLIATO DI INSTANZIARE UN SERVICE DENTRO UNA CLASSE
// class MarketingService
// {
//     protected MailGunService $emailService; 

//     function __construct()
//     {
//         $this->emailService = new MailGunService(); // primo principio SOLID (single : ogni classe deve avere una sola funzione)
//                                                     // non e' corretto instanziare una classe dentro un'altra classe
//                                                     // secondo principio SOLID (open-close : il modulo deve essere aperto all'estensione ma chiuso alle modifiche)
//                                                     // se voglio cambiare di service devo ritoccare il codice in vari punti
//                                                     // e' difficcile effetuare i test
//                                                     // terzo principio SOLID (dipendency : 2 classe non dovono dipendere l'una dall'altra ma da astrazioni)
//     }

//     function sendCampain($body)
//     {
//         $this->emailService->sendEmail($body);
//     }
// }


// MODO SBAGLIATO PASSARLO COME DIPENDENZA DENTRO IL COSTRUTTORE
// class MarketingService
// {
//     protected MailGunService $emailService; 

//     function __construct(MailgunService $emailService) // dipendency injection : risolve solo il primo principio SOLID
//     {
//         $this->emailService = $emailService();
//     }

//     function sendCampain($body)
//     {
//         $this->emailService->sendEmail($body);
//     }
// }
// SENZA SERVICE CONTAINER
$mailgunService = new MailgunService();
$marketingService = new MarketingService($emailService);
$marketingService->sendCampain("Body for the email to send");

// MODO CORRETTO CREARE UN'INTERFACCIA
class MarketingService
{
    protected MailGunService $emailService; 

    function __construct(EmailServiceInterface $emailService) // risolviamo tutti i problemi dei principi SOLID
    {
        $this->emailService = $emailService();
    }

    function sendCampain($body)
    {
        $this->emailService->sendEmail($body);
    }
}


// CON SERVICE CONTAINER
// $container = new ServiceContainer();
$marketingService = $container->get(MarketingService::class);
$marketingService->sendCampain("Body for the email to send");

// CON SERVICE CONTAINER E BINDING DI INTERFACCIA
// $container = new ServiceContainer();
$container->bind(EmailServiceInterface::class, MailgunService::class); // ogni volta che viene instanziato un EmailServiceInterface ci retuira' un'istanza di MailGunService
// $container->bind(EmailServiceInterface::class, config('service.mail')); // possiamo anche passare un file di configurazione invece della classe specifica

$marketingService = $container->get(MarketingService::class);   // usera' l'interfaccia per instanziare perche' glielo abbiamo detto con il binding
                                                                // quello che fara' dietro le quinte sara':
                                                                // $container->get(EmailServiceInterface::class)
                                                                // return new MailgunService()
                                                                // riassumendo ci ritornera' questo:
                                                                // return new MarketingService(new MailgunService())
$marketingService->sendCampain("Body for the email to send");


// FUNZIONALITA' PRINCIPALI IMPLEMENTATE DAL SERVICE CONTAINER

// 1 - AUTO-WIRING
// QUANDO RICHIEDIAMO UN'ISTANZA SENZA AVER FATTO IL BINDING IL CONTAINER ANDRA' A VEDERE SE E' UNA CLASSE REGISTRATA DENTRO IL CONTAINER SE COSI' NON FOSSE NE CREERA' UNA NUOVA BASANDOSI NEL NOME CHE ABBIAMO RICHIESTO
// $marketingService = $container->get(NotRegisteredService::class);
// instanziera' una classe nuova basatta nel nome NotRegisteredService

// 2 - BINDING (bind di 2 classi esempio visto sopra di binding tra interfaccia e classe)
// $container->bind(EmailServiceInterface::class, MailgunService::class);

// 3 - SINGLETON (puo' essere instanziata una sola volta)
// $container->singleton(EmailServiceInterface::class, MailgunService::class);
// ci ristituira' l'unica istanza presente nel container invece di creare una nuova ogni volta

// ESEMPIO PRATICO DI CREAZIONE DI UN SERVICE CONTAINER PARTENDO DAI TEST, INSERITO NEL PROGETTO COMPANIES-API