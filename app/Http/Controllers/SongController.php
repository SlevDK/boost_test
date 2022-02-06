<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSongEntryRequest;
use App\Http\Resources\SongResource;
use App\Repositories\SongRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SongController extends Controller
{
    protected $repo;

    public function __construct(SongRepository $repo)
    {
        /**
         * Since we don't have some complex logic that require `service` classes in our app,
         * lets abstract database IO operations by repository, to keep Model class clear
         * and help in our future refactoring.
         */
        $this->repo = $repo;
    }

    /**
     * Create new song entry
     * Check corresponding request validation for required fields
     *
     * @param CreateSongEntryRequest $request
     * @return JsonResponse
     */
    public function create(CreateSongEntryRequest $request)
    {
        $attr = $request->only('email', 'duration');
        $attr['ip'] = $request->ip();

        try {

            $model = $this->repo->create($attr);

        } catch (\InvalidArgumentException $e) {
            // Repo has some base params checking logic,
            // so lets support validation exception response structure
            return response()->json([
                'errors' => [$e->getMessage()]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } finally {
            // We can do some stuff here, like close file handlers,
            // put connections into connection pools, etc. :)
            // This block will be executed if nothing was thrown or prev catch returns was fired
        }

        return response()->json(new SongResource($model), Response::HTTP_OK);
    }
}
