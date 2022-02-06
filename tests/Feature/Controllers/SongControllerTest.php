<?php

namespace Tests\Feature\Controllers;

use App\Models\Song;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function testThatSongEntryCanBeCreatedCorrectly()
    {
        $song = Song::factory()->create([
            'duration' => 10,
            'total_duration' => 20,
        ]);

        $duration = 20;
        $route = route('create-song-entry', [
            'email' => $song->email, 'duration' => $duration,
        ]);

        $this->getJson($route)->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'email' => $song->email,
                'duration' => $duration,
                'total_duration' => $song->total_duration + $duration,
            ]);

        $this->assertDatabaseCount('songs', 2);
    }

    public function testSongEntryCreateValidation()
    {
        // Test incorrect email
        $route = route('create-song-entry', [
            'email' => 'incorrect', 'duration' => 10,
        ]);

        $this->getJson($route)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => ['email'],
            ]);

        // Test correct but missed in database email
        $route = route('create-song-entry', [
            'email' => 'correct@nonexist.email', 'duration' => 10,
        ]);

        $this->getJson($route)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => ['email'],
            ]);

        // Test incorrect duration
        $song = Song::factory()->create();
        $route = route('create-song-entry', [
            'email' => $song->email, 'duration' => 'incorrect',
        ]);
        $this->getJson($route)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => ['duration'],
            ]);


        // Test duration less than 0
        $route = route('create-song-entry', [
            'email' => $song->email, 'duration' => -1,
        ]);
        $this->getJson($route)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => ['duration'],
            ]);

        // Test duration greater than INT24_MAX
        $route = route('create-song-entry', [
            'email' => $song->email, 'duration' => 2 ** 24 + 1,
        ]);
        $this->getJson($route)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors' => ['duration'],
            ]);
    }
}
