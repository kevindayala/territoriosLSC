<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Territory;
use App\Models\TerritoryAssignment;
use App\Models\Person;
use App\Models\City;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LscFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\InitialDataSeeder::class);
    }

    public function test_user_can_assign_territory()
    {
        $user = User::factory()->create();
        $user->assignRole('publicador');

        $territory = Territory::create([
            'code' => 'TEST-01',
            'city_id' => City::first()->id,
            'neighborhood_name' => 'N1',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->post(route('assignments.store'), [
                'territory_id' => $territory->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('territory_assignments', [
            'territory_id' => $territory->id,
            'assigned_to_user_id' => $user->id,
            'completed_at' => null,
        ]);
    }

    public function test_user_can_complete_territory()
    {
        $user = User::factory()->create();
        $user->assignRole('publicador');

        $territory = Territory::create([
            'code' => 'TEST-02',
            'city_id' => City::first()->id,
            'neighborhood_name' => 'N1',
            'status' => 'active',
        ]);

        $assignment = TerritoryAssignment::create([
            'territory_id' => $territory->id,
            'assigned_to_user_id' => $user->id,
            'assigned_by_user_id' => $user->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->put(route('assignments.update', $assignment));

        $response->assertRedirect();

        $this->assertNotNull($assignment->fresh()->completed_at);
        $this->assertNotNull($territory->fresh()->last_completed_at);
    }

    public function test_admin_can_approve_person()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $territory = Territory::create([
            'code' => 'TEST-03',
            'city_id' => City::first()->id,
            'neighborhood_name' => 'N1',
            'status' => 'active',
        ]);

        $person = Person::create([
            'full_name' => 'John Doe',
            'address' => '123 St',
            'territory_id' => $territory->id,
            'created_by_user_id' => $admin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('persons.update', $person), [
                'approve' => '1',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('active', $person->fresh()->status);
        $this->assertNotNull($person->fresh()->approved_at);
    }

    public function test_non_admin_cannot_approve_person()
    {
        $user = User::factory()->create();
        $user->assignRole('publicador');

        $territory = Territory::create([
            'code' => 'TEST-04',
            'city_id' => City::first()->id,
            'neighborhood_name' => 'N1',
            'status' => 'active',
        ]);

        $person = Person::create([
            'full_name' => 'John Doe',
            'address' => '123 St',
            'territory_id' => $territory->id,
            'created_by_user_id' => $user->id,
            'status' => 'pending',
        ]);

        // Attempting to send approval param
        $response = $this->actingAs($user)
            ->put(route('persons.update', $person), [
                'approve' => '1',
            ]);

        // Should NOT have approved
        $this->assertEquals('pending', $person->fresh()->status);
    }

    public function test_user_can_view_assignments_index()
    {
        $user = User::factory()->create();
        $user->assignRole('publicador');

        $response = $this->actingAs($user)
            ->get(route('assignments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('assignments.index');
    }

    public function test_admin_can_manage_territories()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // 1. Create
        $response = $this->actingAs($admin)
            ->post(route('territories.store'), [
                'code' => 'NEW-01',
                'city_id' => City::first()->id,
                'neighborhood_name' => 'New Hood',
                'status' => 'active',
                'notes' => 'Created via test',
            ]);

        $response->assertRedirect(route('territories.index'));
        $this->assertDatabaseHas('territories', ['code' => 'NEW-01']);

        $territory = Territory::where('code', 'NEW-01')->first();

        // 2. Edit
        $response = $this->actingAs($admin)
            ->put(route('territories.update', $territory), [
                'code' => 'NEW-01-UPDATED',
                'city_id' => City::first()->id,
                'neighborhood_name' => 'Updated Hood',
                'status' => 'inactive',
            ]);

        $response->assertRedirect(route('territories.index'));
        $this->assertDatabaseHas('territories', ['code' => 'NEW-01-UPDATED', 'status' => 'inactive']);

        // 3. Delete
        $response = $this->actingAs($admin)
            ->delete(route('territories.destroy', $territory));

        $response->assertRedirect(route('territories.index'));
        $this->assertSoftDeleted('territories', ['id' => $territory->id]);
    }

    public function test_user_can_filter_territories()
    {
        $user = User::factory()->create();
        $user->assignRole('publicador');

        // Create specific cities and territories
        // Create specific cities and territories
        $city1 = City::create(['name' => 'City A', 'slug' => 'city-a', 'is_active' => true]);
        $city2 = City::create(['name' => 'City B', 'slug' => 'city-b', 'is_active' => true]);

        $t1 = Territory::create([
            'code' => 'T-A-1',
            'city_id' => $city1->id,
            'neighborhood_name' => 'N1',
            'status' => 'active',
        ]);

        $t2 = Territory::create([
            'code' => 'T-B-1',
            'city_id' => $city2->id,
            'neighborhood_name' => 'N2',
            'status' => 'inactive',
        ]);

        // Filter by City 1
        $response = $this->actingAs($user)
            ->get(route('territories.index', ['city_id' => $city1->id]));

        $response->assertSee('T-A-1');
        $response->assertDontSee('T-B-1');

        // Filter by Status Inactive
        $response = $this->actingAs($user)
            ->get(route('territories.index', ['status' => 'inactive']));

        $response->assertSee('T-B-1');
        $response->assertDontSee('T-A-1');
    }

    public function test_pdf_export_availability()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)
            ->get(route('export.assignments', ['year' => 2026]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
    public function test_admin_creation_auto_approves_person()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Ensure we have a territory
        $territory = Territory::first();
        // If no territory exists (fresh factory run might not seed), create one
        if (!$territory) {
            $city = City::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true]);
            $territory = Territory::create(['code' => 'T1', 'city_id' => $city->id, 'neighborhood_name' => 'T']);
        }

        $response = $this->actingAs($admin)
            ->post(route('persons.store'), [
                'full_name' => 'Admin Created Person',
                'address' => '123 Admin Way',
                'territory_id' => $territory->id,
            ]);

        $response->assertRedirect(route('persons.index'));
        $this->assertDatabaseHas('persons', [
            'full_name' => 'Admin Created Person',
            'status' => 'active', // Should be active immediately
        ]);
    }
}
