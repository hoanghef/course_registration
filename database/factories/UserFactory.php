<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // File: database/factories/UserFactory.php

public function definition(): array
{
    return [
        // Sửa 'name' thành 'full_name' và 'username'
        'full_name' => fake()->name(), // Dùng fake()->name() để tạo tên đầy đủ
        'username' => fake()->unique()->userName(), // Dùng fake()->userName() để tạo username không trùng lặp

        // Các trường khác giữ nguyên
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= Hash::make('password'), // Mật khẩu mặc định là 'password'
        'remember_token' => Str::random(10),

        // Thêm các trường bạn đã định nghĩa
        'phone' => fake()->phoneNumber(),
        'address' => fake()->address(),
        'role' => fake()->randomElement(['admin', 'phong_dao_tao', 'giang_vien', 'sinh_vien']),
        'is_active' => true,
    ];
}

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
