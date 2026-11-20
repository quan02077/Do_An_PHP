<?php

namespace Tests\Feature;

use App\Models\SuKien;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

/** Ảnh mẫu nằm trong git (database/seeders/images) và được seeder chép sang public/uploads/su_kien. */
class SeederImagesTest extends TestCase
{
    public function test_every_seeded_event_image_exists_on_disk(): void
    {
        $this->seed(DatabaseSeeder::class);

        $events = SuKien::all();
        $this->assertCount(12, $events);

        foreach ($events as $event) {
            $this->assertFileExists(
                public_path('uploads/su_kien/'.$event->hinh_anh),
                "Sự kiện #{$event->id} trỏ tới ảnh không tồn tại: {$event->hinh_anh}"
            );
        }
    }

    public function test_copied_images_match_the_versioned_sources(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (glob(database_path('seeders/images/*.jpg')) as $source) {
            $this->assertFileEquals($source, public_path('uploads/su_kien/'.basename($source)));
        }
    }
}
