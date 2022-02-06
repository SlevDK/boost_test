<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSongEntryRequest;
use App\Http\Requests\SongListRequest;
use App\Http\Resources\SongResource;
use App\Http\Resources\SongResourceCollection;
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

    public function index(SongListRequest $request)
    {
        $per_page = (int) $request->input('per_page', 50);
        $order = $request->input('order_by', 'id');
        $direction = $request->input('order_direction', 'asc');

        $filters = [];
        if ($request->has('total_duration') && $request->has('total_duration_condition')) {
            $total_d = (int) $request->input('total_duration');
            $total_d_cond = $request->input('total_duration_condition');
            $filters[] = ['total_duration', $total_d_cond, $total_d];
        }

        $songs = $this->repo->paginatedList($filters, $per_page, $order, $direction);

        return response()->json(new SongResourceCollection($songs), Response::HTTP_OK);
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
