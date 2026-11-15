<?php

namespace Database\Factories;

use App\Models\JenisPrestasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class JenisPrestasiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JenisPrestasi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => 'JP-' . $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->sentence(3),
            'kategori' => $this->faker->randomElement(['Akademik', 'Non-Akademik']),
            'tingkat' => $this->faker->randomElement(['Internasional', 'Nasional', 'Regional', 'Lokal']),
            'is_active' => true,
        ];
    }
}