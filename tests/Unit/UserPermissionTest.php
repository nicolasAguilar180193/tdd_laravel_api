<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function can_assign_permissions_to_a_user(): void
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }

    /** @test */
    public function can_assign_the_same_permission_twice(): void
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
}
