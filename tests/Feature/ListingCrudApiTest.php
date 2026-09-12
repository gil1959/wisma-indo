<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\User;
use App\Models\UserQuota;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListingCrudApiTest extends TestCase
{
    private $owner;
    private $categories;

    protected function setUp(): void
    {
        parent::setUp();
        // Never migrate or write into the developer's configured database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique(); $t->string('email')->unique();
            $t->string('password'); $t->string('phone')->nullable(); $t->timestamp('email_verified_at')->nullable(); $t->timestamps();
        });
        Schema::create('listing_categories', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug'); $t->string('type'); $t->timestamps();
        });
        Schema::create('roles', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('guard_name'); $t->timestamps(); });
        Schema::create('model_has_roles', function (Blueprint $t) { $t->unsignedBigInteger('role_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id'); });
        foreach ([
            '2026_07_10_000000_create_property_listings_tables.php' => 'CreatePropertyListingsTables',
            '2026_07_13_083026_add_comprehensive_fields_to_listings_table.php' => 'AddComprehensiveFieldsToListingsTable',
            '2026_07_13_102658_add_maps_url_to_listings_table.php' => 'AddMapsUrlToListingsTable',
            '2026_07_17_133527_add_lat_lng_to_listings_table.php' => 'AddLatLngToListingsTable',
        ] as $file => $class) {
            require_once database_path('migrations/' . $file);
            (new $class)->up();
        }
        Schema::table('user_quotas', function (Blueprint $t) { $t->integer('listing_quota')->default(0); });
        Schema::create('settings', function (Blueprint $t) { $t->id(); $t->string('key'); $t->text('value')->nullable(); });
        Storage::fake('public');
        $this->owner = User::create(['name' => 'Listing Test Owner', 'email' => 'listing-owner@example.test', 'password' => bcrypt('unused'), 'phone' => '08123456789', 'email_verified_at' => now()]);
        UserQuota::create(['user_id' => $this->owner->id, 'listing_quota' => 3]);
        $this->categories = [];
        foreach (['property', 'goods', 'services'] as $type) {
            $this->categories[$type] = ListingCategory::create(['name' => ucfirst($type), 'slug' => $type, 'type' => $type]);
        }
        Sanctum::actingAs($this->owner);
    }

    private function payload(string $type = 'property'): array
    {
        return ['type' => $type, 'listing_category_id' => $this->categories[$type]->id,
            'title' => 'Iklan pengujian ' . $type, 'description' => 'Deskripsi iklan pengujian', 'price' => '125000.50',
            'location' => 'Jakarta Selatan', 'address' => 'Jalan Melati 10', 'whatsapp' => '08123456789',
            'transaction_type' => $type === 'property' ? 'disewa' : null, 'condition' => $type === 'goods' ? 'Bekas' : null,
            'brand' => $type === 'goods' ? 'Merek contoh' : null, 'service_area' => $type === 'services' ? 'Jabodetabek' : null,
            'latitude' => -6.2345, 'longitude' => 106.8123, 'maps_url' => 'https://www.google.com/maps?q=-6.2345,106.8123',
            'negotiable' => false, 'imb' => false, 'pbb' => false, 'co_broke' => false,
            'facilities' => [], 'surroundings' => []];
    }
    public function test_unlimited_package_quota_stays_unlimited_after_creation()
    {
        $this->owner->quota()->update(['listing_quota'=>-1]);
        $this->getJson('/api/v1/user/listing-form')->assertOk()->assertJsonPath('data.can_create',true);
        $this->createListing($this->payload())->assertCreated();
        $this->assertSame(-1,(int)$this->owner->quota()->first()->listing_quota);
    }

    private function createListing(array $payload, array $extra = [])
    {
        return $this->post('/api/v1/user/listings', array_merge([
            'payload' => json_encode($payload), 'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ], $extra), ['Accept' => 'application/json']);
    }

    public function test_all_types_round_trip_and_consume_exactly_one_quota_each()
    {
        foreach (['property', 'goods', 'services'] as $type) {
            $payload = $this->payload($type);
            if ($type === 'property') $payload += ['land_area' => 120, 'building_area' => 90, 'rental_period' => 'Tahunan', 'min_rental' => '2 tahun', 'furnished_status' => 'Semi Furnished'];
            $result = $this->createListing($payload, ['images' => [UploadedFile::fake()->image('gallery.png')]])
                ->assertCreated()->assertJsonPath('data.type', $type)->assertJsonPath('data.negotiable', 0);
            $id = $result->json('data.id');
            $this->getJson('/api/v1/user/listings/' . $id)->assertOk()
                ->assertJsonPath('data.listing_category_id', $this->categories[$type]->id)
                ->assertJsonPath('data.location', 'Jakarta Selatan')->assertJsonCount(1, 'data.images');
            $listing = Listing::findOrFail($id);
            $this->assertEquals(-6.2345, $listing->latitude);
            $this->assertEquals(106.8123, $listing->longitude);
            if ($type === 'goods') $this->assertSame('Merek contoh', $listing->brand);
            if ($type === 'services') $this->assertSame('Jabodetabek', $listing->service_area);
        }
        $this->assertSame(0, (int) $this->owner->quota()->first()->listing_quota);
        $this->getJson('/api/v1/user/listing-form')->assertOk()->assertJsonPath('data.can_create', false)->assertJsonCount(3, 'data.categories');
        $this->createListing($this->payload())->assertUnprocessable()->assertJsonValidationErrors('listing_quota');
        $this->assertSame(3, Listing::count());
    }

    public function test_edit_replaces_cover_adds_deletes_photos_and_preserves_quota()
    {
        $payload = $this->payload();
        $result = $this->createListing($payload, ['images' => [UploadedFile::fake()->image('old.jpg')]])->assertCreated();
        $id = $result->json('data.id');
        $oldCover = Listing::find($id)->cover_image;
        $payload['title'] = 'Judul diperbarui';
        $payload['latitude'] = -7.1; $payload['longitude'] = 110.2;
        $payload['delete_images'] = [$result->json('data.images.0.id')];
        $payload['facilities'] = ['Kolam Renang'];
        $payload['negotiable'] = true;
        $this->post('/api/v1/user/listings/' . $id, ['_method' => 'PUT', 'payload' => json_encode($payload),
            'cover_image' => UploadedFile::fake()->image('new.jpg'), 'images' => [UploadedFile::fake()->image('newgallery.jpg')]], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('data.title', 'Judul diperbarui')->assertJsonPath('data.negotiable', 1)->assertJsonCount(1, 'data.images');
        $this->assertEquals(-7.1, Listing::find($id)->latitude);
        $this->assertSame(2, (int) $this->owner->quota()->first()->listing_quota);
        Storage::disk('public')->assertMissing(str_replace('/storage/', '', parse_url($oldCover, PHP_URL_PATH)));
        $payload['negotiable'] = false; $payload['facilities'] = []; unset($payload['delete_images']);
        $this->putJson('/api/v1/user/listings/' . $id, $payload)->assertOk()->assertJsonPath('data.negotiable', 0)->assertJsonPath('data.facilities', []);
        $this->deleteJson('/api/v1/user/listings/' . $id)->assertOk();
        $this->assertSame(0, Listing::count());
        $this->assertSame(2, (int) $this->owner->quota()->first()->listing_quota);
        $this->assertCount(0, Storage::disk('public')->allFiles('listings'));
    }

    public function test_invalid_type_category_coordinates_and_files_never_consume_quota()
    {
        $bad = $this->payload('goods'); $bad['listing_category_id'] = $this->categories['property']->id;
        $this->createListing($bad)->assertUnprocessable()->assertJsonValidationErrors('listing_category_id');
        $bad = $this->payload(); $bad['latitude'] = 100;
        $this->createListing($bad)->assertUnprocessable()->assertJsonValidationErrors('latitude');
        $bad = $this->payload(); $bad['longitude'] = null;
        $this->createListing($bad)->assertUnprocessable()->assertJsonValidationErrors('longitude');
        $this->createListing($this->payload(), ['cover_image' => UploadedFile::fake()->create('bad.txt', 1, 'text/plain')])->assertUnprocessable()->assertJsonValidationErrors('cover_image');
        $this->createListing($this->payload(), ['cover_image' => UploadedFile::fake()->image('big.jpg')->size(20481)])->assertUnprocessable()->assertJsonValidationErrors('cover_image');
        $this->assertSame(3, (int) $this->owner->quota()->first()->listing_quota);
        $this->assertSame(0, Listing::count());
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_gallery_limit_is_enforced_across_existing_and_new_images()
    {
        $id = $this->createListing($this->payload())->assertCreated()->json('data.id');
        $listing = Listing::find($id);
        for ($i = 0; $i < 18; $i++) $listing->images()->create(['image_path' => '/storage/listings/test' . $i . '.jpg']);
        $this->post('/api/v1/user/listings/' . $id, ['_method' => 'PUT', 'payload' => json_encode($this->payload()),
            'images' => [UploadedFile::fake()->image('extra.jpg')]], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('images');
        $this->assertSame(18, $listing->images()->count());
    }

    public function test_other_users_cannot_read_edit_delete_or_remove_photos()
    {
        $result = $this->createListing($this->payload(), ['images' => [UploadedFile::fake()->image('photo.jpg')]])->assertCreated();
        $id = $result->json('data.id');
        $other = User::create(['name' => 'Other owner', 'email' => 'other@example.test', 'password' => bcrypt('unused'), 'email_verified_at' => now()]);
        Sanctum::actingAs($other);
        $this->getJson('/api/v1/user/listings/' . $id)->assertNotFound();
        $this->putJson('/api/v1/user/listings/' . $id, $this->payload())->assertNotFound();
        $this->deleteJson('/api/v1/user/listings/' . $id)->assertNotFound();
        $this->getJson('/api/v1/user/listing-form')->assertOk()->assertJsonPath('data.can_create', false);
        $this->createListing($this->payload())->assertUnprocessable()->assertJsonValidationErrors('listing_quota');
        Sanctum::actingAs($this->owner);
        $second = $this->createListing($this->payload())->assertCreated()->json('data.id');
        $payload = $this->payload(); $payload['delete_images'] = [$result->json('data.images.0.id')];
        $this->putJson('/api/v1/user/listings/' . $second, $payload)->assertUnprocessable()->assertJsonValidationErrors('delete_images.0');
        $this->assertSame(1, Listing::find($id)->images()->count());
    }

    public function test_image_failure_rolls_back_quota_and_listing()
    {
        $this->withoutExceptionHandling();
        Storage::shouldReceive('disk')->with('public')->andReturnSelf();
        Storage::shouldReceive('putFileAs')->andReturn(false);
        try { $this->createListing($this->payload()); $this->fail('Expected upload failure'); }
        catch (\RuntimeException $e) { $this->assertSame('Foto tidak dapat disimpan.', $e->getMessage()); }
        $this->assertSame(0, Listing::count());
        $this->assertSame(3, (int) $this->owner->quota()->first()->listing_quota);
    }

    public function test_unverified_account_cannot_bypass_the_web_requirement()
    {
        $this->owner->update(['email_verified_at' => null]);
        $this->getJson('/api/v1/user/listing-form')->assertForbidden();
        $this->createListing($this->payload())->assertForbidden();
        $this->assertSame(3, (int) $this->owner->quota()->first()->listing_quota);
        $this->assertSame(0, Listing::count());
    }

    public function test_web_and_api_share_the_last_quota()
    {
        $this->withoutMiddleware(\App\Http\Middleware\TrackVisitor::class);
        $this->owner->quota()->update(['listing_quota' => 1]);
        $this->actingAs($this->owner, 'web');
        $this->post('/pasang-iklan', array_merge($this->payload('goods'), ['cover_image' => UploadedFile::fake()->image('web.jpg')]))
            ->assertRedirect(route('iklan.saya'));
        $this->assertSame(1, Listing::count());
        $this->assertSame('goods', Listing::first()->type);
        $this->assertSame(0, (int) $this->owner->quota()->first()->listing_quota);
        Sanctum::actingAs($this->owner);
        $this->createListing($this->payload())->assertUnprocessable()->assertJsonValidationErrors('listing_quota');
        $this->assertSame(1, Listing::count());
    }
}
