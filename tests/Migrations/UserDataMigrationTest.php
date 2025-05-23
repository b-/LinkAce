<?php

namespace Tests\Migrations;

use App\Models\Link;
use App\Models\LinkList;
use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use App\Settings\SystemSettings;
use App\Settings\UserSettings;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\SanctumServiceProvider;
use Tests\TestCase;

class UserDataMigrationTest extends TestCase
{
    use MigratesUpTo;

    public function test_link_visibility_migration(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        Link::unguard();
        Link::create([
            'url' => 'https://private-link.com',
            'title' => 'Test',
            'user_id' => 1,
            'is_private' => true,
        ]);

        Link::create([
            'url' => 'https://public-link.com',
            'title' => 'Test',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('links', [
            'url' => 'https://private-link.com',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('links', [
            'url' => 'https://public-link.com',
            'visibility' => 2, // is internal
        ]);
    }

    public function test_link_visibility_migration_with_enabled_guest_mode(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        SystemSettings::fake(['guest_access_enabled' => true]);

        Link::unguard();
        Link::create([
            'url' => 'https://private-link.com',
            'title' => 'Test',
            'user_id' => 1,
            'is_private' => true,
        ]);

        Link::create([
            'url' => 'https://public-link.com',
            'title' => 'Test',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('links', [
            'url' => 'https://private-link.com',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('links', [
            'url' => 'https://public-link.com',
            'visibility' => 1, // is public
        ]);
    }

    public function test_list_visibility_migration(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        LinkList::unguard();
        LinkList::create([
            'name' => 'Private List',
            'user_id' => 1,
            'is_private' => true,
        ]);

        LinkList::create([
            'name' => 'Public List',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('lists', [
            'name' => 'Private List',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('lists', [
            'name' => 'Public List',
            'visibility' => 2, // is internal
        ]);
    }

    public function test_list_visibility_migration_with_enabled_guest_mode(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        SystemSettings::fake(['guest_access_enabled' => true]);

        LinkList::unguard();
        LinkList::create([
            'name' => 'Private List',
            'user_id' => 1,
            'is_private' => true,
        ]);

        LinkList::create([
            'name' => 'Public List',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('lists', [
            'name' => 'Private List',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('lists', [
            'name' => 'Public List',
            'visibility' => 1, // is public
        ]);
    }

    public function test_tag_visibility_migration(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        Tag::unguard();
        Tag::create([
            'name' => 'PrivateTag',
            'user_id' => 1,
            'is_private' => true,
        ]);

        Tag::create([
            'name' => 'PublicTag',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('tags', [
            'name' => 'PrivateTag',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'PublicTag',
            'visibility' => 2, // is internal
        ]);
    }

    public function test_tag_visibility_migration_with_enabled_guest_mode(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        SystemSettings::fake(['guest_access_enabled' => true]);

        Tag::unguard();
        Tag::create([
            'name' => 'PrivateTag',
            'user_id' => 1,
            'is_private' => true,
        ]);

        Tag::create([
            'name' => 'PublicTag',
            'user_id' => 1,
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('tags', [
            'name' => 'PrivateTag',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'PublicTag',
            'visibility' => 1, // is public
        ]);
    }

    public function test_note_visibility_migration(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        Note::unguard();
        Note::create([
            'user_id' => 1,
            'link_id' => 1,
            'note' => 'A private note',
            'is_private' => true,
        ]);

        Note::create([
            'user_id' => 1,
            'link_id' => 1,
            'note' => 'A public note',
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('notes', [
            'note' => 'A private note',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('notes', [
            'note' => 'A public note',
            'visibility' => 2, // is internal
        ]);
    }

    public function test_note_visibility_migration_with_enabled_guest_mode(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        SystemSettings::fake(['guest_access_enabled' => true]);

        Note::unguard();
        Note::create([
            'user_id' => 1,
            'link_id' => 1,
            'note' => 'A private note',
            'is_private' => true,
        ]);

        Note::create([
            'user_id' => 1,
            'link_id' => 1,
            'note' => 'A public note',
            'is_private' => false,
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('notes', [
            'note' => 'A private note',
            'visibility' => 3, // is private
        ]);
        $this->assertDatabaseHas('notes', [
            'note' => 'A public note',
            'visibility' => 1, // is public
        ]);
    }

    public function test_user_api_token_migration(): void
    {
        $this->migrateUpTo('2022_06_23_112431_migrate_user_data.php');

        DB::table('users')->insert([
            'name' => 'test',
            'email' => 'test@linkace.org',
            'password' => 'test',
            'api_token' => 'testApiToken',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('migrate');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => '1',
            'name' => 'MigratedApiToken',
            'abilities' => '["user_access"]',
        ]);

        // Test if the token is valid
        Link::factory()->for(User::find(1))->create(['url' => 'https://token-test.com']);

        $this->get('links/feed', [
            'Authorization' => 'Bearer testApiToken'
        ])->assertOk()->assertSee('https://token-test.com');
    }
}
