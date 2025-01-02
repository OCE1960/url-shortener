<?php

namespace Tests\Feature;

use App\Models\Url;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_decode_url_endpoint_was_successful(): void
    {
        $url = Url::factory()->create();

        $response = $this->getJson('api/1/decode?shortened_url=' . $url->shortened_url);

        $response->assertStatus(200)
            ->assertJsonStructure(['url']);
    }

    public function test_decode_url_endpoint_requires_shortened_url_params(): void
    {

        $response = $this->getJson('api/1/decode?shortened_url=');

        $response->assertStatus(422)
            ->assertJson([
                "errors" => ["shortened_url" => ["The Shortened Url field is required."]]
            ]);
    }

    public function test_decode_url_endpoint_requires_valid_url_params(): void
    {

        $response = $this->getJson('api/1/decode?shortened_url=invalid_url');

        $response->assertStatus(422)
            ->assertJson([
                "errors" => ["shortened_url" => ["The Shortened Url provided is not a valid url."]]
            ]);
    }

    public function test_encode_url_end_point_requires_original_url_field(): void
    {

        $response = $this->postJson('api/1/encode', [
            'original_url' => null,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                "errors" => ["original_url" => ["The Original Url field is required."]]
            ]);
    }

    public function test_encode_url_end_point_requires_original_url_value_must_be_valid(): void
    {
        $originalUrl = 'testing';

        $response = $this->postJson('api/1/encode', [
            'original_url' => $originalUrl,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                "errors" => ["original_url" => ["The Original Url provided is not a valid url."]]
            ]);
    }

    public function test_encode_url_end_point_was_successful(): void
    {
        $url = $this->faker()->url();
        $response = $this->postJson('api/1/encode', [
            'original_url' => $this->faker()->url(),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['url']);
    }
}
