<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'guardia', 'guard_name' => 'web']);
});

test('api login returns token for guardia', function () {
    $user = User::factory()->create([
        'email' => 'guardia@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('guardia');

    $this->postJson('/api/login', [
        'email' => 'guardia@test.com',
        'password' => 'password',
        'device_name' => 'test-device',
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['token', 'user']);
});

test('verificar qr rejects invalid json', function () {
    $user = User::factory()->create();
    $user->assignRole('guardia');

    $this->actingAs($user)
        ->postJson('/verificaciones/qr', ['codigo_qr' => 'no-json'])
        ->assertStatus(422);
});

test('debug qr route is not available outside local', function () {
    $this->postJson('/debug/qr', ['codigo_qr' => '{}'])
        ->assertNotFound();
});
