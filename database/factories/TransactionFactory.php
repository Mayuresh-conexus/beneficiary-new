<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Package;
use App\Models\User;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'currency' => 'USD',
            'status' => 'pending',
            'delivery_date' => null,
            'remarks' => $this->faker->sentence(),

            // Generate related models if not provided
            'beneficiary_id' => Beneficiary::factory(),
            'project_id' => Project::factory(),
            'package_id' => Package::factory(),
            'financial_officer_id' => User::factory(),
            'delivered_by' => null,
        ];
    }
}
