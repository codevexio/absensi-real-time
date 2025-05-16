<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class KaryawanFactory extends Factory
{
    protected $model = \App\Models\Karyawan::class;

    public function definition()
    {
        $golonganOptions = ['A', 'B', 'C', 'D', 'E'];
        $divisiOptions = ['A', 'B', 'C', 'D', 'E'];

        return [
            'nama' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'), 
            'golongan' => $this->faker->randomElement($golonganOptions),
            'divisi' => $this->faker->randomElement($divisiOptions),
        ];
    }
}
