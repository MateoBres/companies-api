<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $type = $this->faker->randomElement([1 ,2, 3, 4]);
        if($type==4){
            $taxCode = $this->faker->regexify('[A-Za-z0-9]{16}');
        } else {
            $taxCode = $this->faker->regexify('[0-9]{11}');
        }

        return [
            'businessName' => $this->faker->sentence,
            'address' => $this->faker->sentence,
            'vat' => $this->faker->regexify('[0-9]{11}'),
            'taxCode' => $taxCode,
            'employees' => $this->faker->randomNumber(5, false),
            'active'=> $this->faker->boolean(),
            'type' => $type
        ];
    }
}