<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

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
