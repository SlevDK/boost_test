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

    public function testThatSongsListCanBeReturned()
    {
        $song1 = Song::factory()->create();
        $song2 = Song::factory()->create();

        $this->getJson(route('songs-list'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'id'            => $song1->id,
                'name'          => $song1->name,
                'email'         => $song1->email,
                'duration'      => $song1->duration,
                'total_duration' => $song1->total_duration,
                'created_at'    => $song1->created_at,
            ])
            ->assertJsonFragment([
                'id'            => $song2->id,
                'name'          => $song2->name,
                'email'         => $song2->email,
                'duration'      => $song2->duration,
                'total_duration' => $song2->total_duration,
                'created_at'    => $song2->created_at,
            ]);
    }

    public function testSongsListPagination()
    {
        Song::factory()->count(10)->create();
        $notInResponse = Song::factory()->create();

        $per_page = 10;
        $this->getJson(route('songs-list', ['per_page' => $per_page]))
            ->assertJsonCount($per_page, 'data')
            ->assertJsonMissing([
                'id' => $notInResponse->id,
            ]);
    }

    //TODO: test list order_by fields, test list order_direction fields

    public function testThatListCanBeFilteredByTotalDuration()
    {
        $td1 = 100;
        $song1 = Song::factory()->create([
            'total_duration' => $td1,
        ]);

        $td2 = 200;
        $song2 = Song::factory()->create([
            'total_duration' => $td2,
        ]);

        $this->getJson(route('songs-list', ['total_duration' => 200, 'total_duration_condition' => '<']))
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $song1->id,
                'total_duration' => $td1,
            ])
            ->assertJsonMissing([
                'id' => $song2->id,
                'total_duration' => $td2,
            ]);

        $this->getJson(route('songs-list', ['total_duration' => 100, 'total_duration_condition' => '>']))
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $song2->id,
                'total_duration' => $td2,
            ])
            ->assertJsonMissing([
                'id' => $song1->id,
                'total_duration' => $td1,
            ]);

        $td3 = 300;
        $song3 = Song::factory()->create([
            'total_duration' => $td3,
        ]);

        $this->getJson(route('songs-list', ['total_duration' => 300, 'total_duration_condition' => '=']))
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $song3->id,
                'total_duration' => $td3,
            ]);
    }
}
