<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Tests\ClassFake\AClass;
use Tests\ClassFake\AnInterface;
use App\Services\ServiceContainer;
use Tests\ClassFake\ASingletonClass;
use Tests\ClassFake\AClassWithDependency;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceContainerTest extends TestCase
{
    use RefreshDatabase;

    // preparazione dati per i test
    protected $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container =  new ServiceContainer;
    }

    
    /** @test */
    public function it_can_do_the_test()
    {
        $this->assertTrue(true);
    }
  
    /** @test */
    public function it_can_construct_a_new_instance_from_the_basename_if_not_binding_exists()
    {
        $instance = $this->container->get(AClass::class); // instanza di una classe classe qualsiasi

        $this->assertInstanceOf(AClass::class, $instance);
    }
    
    /** @test */
    public function it_returns_an_instance_of_the_bounded_if_binding_exists()
    {
        $this->container->bind(AnInterface::class, AClass::class); // binding con interfaccia

        $instance = $this->container->get(AnInterface::class); // deve ritornarci una AClass 

        $this->assertInstanceOf(AClass::class, $instance); // verifico che ci ritorni una AClass 
    }

    /** @test */
    public function it_can_resolve_dependencies()
    {
        $instance = $this->container->get(AClassWithDependency::class);

        $this->assertInstanceOf(AClass::class, $instance->dependency); // verifico che ci ritorni una AClass perche' e' dipendency nel costruttore di AClassWithDependency
    }
    
    /** @test */
    public function it_can_resolve_singleton_istances()
    {
        $this->container->singleton(AnInterface::class, ASingletonClass::class); // binding singleton con interfaccia e classe singleton

        $instance = $this->container->get(AnInterface::class); // deve ritornarci una AClass 

        $this->assertEquals(0, $instance->value); // verifico che ci ritorni la proprieta value della classe singleton 

        // incrementiamo il value dell'instance per vedere se l'abbiamo modificato nel singleton
        $instance->value++;

        $instance = $this->container->get(AnInterface::class); // deve ritornarci una AClass 

        $this->assertEquals(1, $instance->value); // verifico che ci ritorni la proprieta value della classe singleton incrementato
    }

    /** @test */
    public function it_can_bind_closures()
    {
        $this->container->bind(AnInterface::class, function ($container) {
            return 'ok';
        });

        $instance = $this->container->get(AnInterface::class);

        $this->assertEquals('ok', $instance);
    }
}