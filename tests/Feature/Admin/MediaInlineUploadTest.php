<?php

namespace Tests\Feature\Admin;

use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaInlineUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_upload_inline_image_and_media_asset_is_created(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $response = $this->postJson(route('admin.media.store'), [
            'image' => UploadedFile::fake()->image('inline.jpg', 900, 600),
            'alt_text' => 'Route SMS en production',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['id', 'path', 'url', 'filename'])
            ->assertJsonPath('filename', fn (string $filename) => str_ends_with($filename, '.jpg'));

        $path = $response->json('path');
        $this->assertStringStartsWith('/img/cms/pages/', $path);
        $this->assertFileExists(public_path(ltrim($path, '/')));
        $this->assertDatabaseHas('media_assets', [
            'path' => $path,
            'alt_text' => 'Route SMS en production',
        ]);

        File::delete(public_path(ltrim($path, '/')));
    }

    public function test_inline_upload_rejects_svg_and_oversized_files(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $this->postJson(route('admin.media.store'), [
            'image' => UploadedFile::fake()->create('inline.svg', 12, 'image/svg+xml'),
        ])->assertUnprocessable();

        $this->postJson(route('admin.media.store'), [
            'image' => UploadedFile::fake()->image('huge.jpg')->size(5121),
        ])->assertUnprocessable();
    }

    public function test_viewer_cannot_upload_inline_media(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_VIEWER]));

        $this->postJson(route('admin.media.store'), [
            'image' => UploadedFile::fake()->image('inline.jpg', 900, 600),
        ])->assertForbidden();
    }

    public function test_media_used_inside_content_blocks_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $directory = public_path('img/cms/pages');
        File::ensureDirectoryExists($directory);
        $path = '/img/cms/pages/inline-used.jpg';
        File::put(public_path(ltrim($path, '/')), UploadedFile::fake()->image('inline-used.jpg', 900, 600)->get());

        $asset = MediaAsset::create([
            'path' => $path,
            'filename' => 'inline-used.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => filesize(public_path(ltrim($path, '/'))),
        ]);

        Page::create([
            'slug' => 'inline-used',
            'section' => 'blog',
            'locale' => 'fr',
            'title' => 'Inline used',
            'content_blocks' => [
                'sections' => [
                    [
                        'heading' => 'Image',
                        'body' => 'Image inline',
                        'body_html' => '<p><img src="/img/cms/pages/inline-used.jpg" alt="Inline"></p>',
                    ],
                ],
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->delete(route('admin.media.destroy', $asset))
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media_assets', ['id' => $asset->id]);
        $this->assertFileExists(public_path(ltrim($path, '/')));

        File::delete(public_path(ltrim($path, '/')));
    }
}
