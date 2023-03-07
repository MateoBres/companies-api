<?php

namespace App\Services;

class ServiceContainer
{
    protected $bindings = [];

    protected $singletonBindings = [];

    public function get(string $abstract)
    {
        // considero il caso di una get in caso di binding
        if ($this->hasBindings($abstract)) {
            // return new $this->bindings[$abstract]();
            return $this->resolve($this->bindings[$abstract]);
        }
        // considero il caso di una get in caso di singleton binding
        if ($this->hasSingleton($abstract)) {
            // return new $this->bindings[$abstract]();
            return $this->singletonBindings[$abstract];
        }
        // return new $abstract();
        return $this->resolve($abstract);
    }

    public function bind(string $abstract, string|\Closure $concrete) // permetto tipo stringa o closure(funzione)
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function hasBindings(string $abstract)
    {
        return array_key_exists($abstract, $this->bindings);
    }

    protected function resolve(string|\Closure $abstract)
    {
        if($abstract instanceof \Closure){
            return $abstract($this); //eseguiamo la funzione closure con parametro lo stesso service container
        }
       // spostato tutto nel metodo makeInstanceOf
       return $this->makeNewInstanceOf($abstract);
    }

    public function resolveDependencies(array $parameters)
    {
        $dependencies = [];

        // foreach ($parameters as $parameter){
        //     $dependencies[] = $this->get($parameter->getType());
        // }

        //refactoring del foreach
        // $dependencies = array_map(function ($parameter) {
        //     return $this->get($parameter->getType());
        // }, $parameters);

        // refactoring con arrow function e senza variabile dependencies
        return array_map(fn($parameter) => $this->get($parameter->getType()), $parameters);

        // return $dependencies;
    }

    public function singleton(string $abstract, string $concrete)
    {
        $this->singletonBindings[$abstract] = $this->get($concrete);
    }

    public function hasSingleton(string $abstract)
    {
        return array_key_exists($abstract, $this->singletonBindings);
    }

    protected function makeNewInstanceOf($abstract)
    {
          //controllare il costruttore di $abstract (usiamo la reflection)
          $r = new \ReflectionClass($abstract);
          $constructor = $r->getConstructor(); // o riceve null se non esiste il construttore ma solo quello di default che non riceve attributi
  
          // risolvere le dipendenze di $abstract
          if (!$constructor) {
              return new $abstract; // se non abbiamo un contruttore instanzio normalmente
          }
  
          $dependencies = $this->resolveDependencies($constructor->getParameters());
  
          // costruire una nuova istanza passando le dipendenze
          // return new $abstract($dependencies[0], $dependencies[1], $dependencies[2], 'etc...');  //usiamo un metodo per passarle cosi' in modo dinamico  
          return new $abstract(...$dependencies); // operatore di unpacking
    }
}
