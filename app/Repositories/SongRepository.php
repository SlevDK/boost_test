<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SongRepository
{
    protected $model;

    public function __construct(string $model_namespace)
    {
        $this->model = $model_namespace;
    }

    /**
     * Returns collection of items.
     * @param int $per_page     If per_page = -1 all matched rows will be returned.
     * @param string $order
     * @param string $direction
     * @return mixed
     */
    public function list(array $filters = [], int $per_page = -1, string $order = 'id', string $direction = 'asc'): Collection
    {
        return $this->paginatedList([], $per_page, $order, $direction)->all();
    }

    /**
     * @param array $filters
     * @param int $per_page If per_page = -1 all matched rows will be returned.
     * @param string $order
     * @param string $direction
     * @return mixed
     */
    public function paginatedList(
        array $filters = [], int $per_page = -1, string $order = 'id', string $direction = 'asc'
    ): LengthAwarePaginator {

        /* $q->where('field', '<', value)->where(... */
        $q = $this->applyFilters($this->model::query(), $filters);

        return $q->orderBy($order, $direction)->paginate($per_page);
    }

    /**
     * Create new song entry.
     * Provided email `must` already exist in table.
     * @param array $attr
     * @return Model
     */
    public function create(array $attr): Model
    {
        if (! isset($attr['duration']) || $attr['duration'] < 0) {
            throw new \InvalidArgumentException("Duration must be greater or equal 0");
        }

        if (! isset($attr['email']) || ! filter_var($attr['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        if (! isset($attr['ip']) || ! filter_var($attr['ip'], FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('Invalid ip address');
        }

        $lastEntry = $this->lastEmailEntry($attr['email']);

        return $this->model::create([
            'email'     => $attr['email'],
            'name'      => $lastEntry->name,
            'ip'        => $attr['ip'],
            'duration'  => $attr['duration'],
            'total_duration' => $lastEntry->total_duration + $attr['duration'],
        ]);
    }

    /**
     * We can process filters as array of arrays with params, instead of writing
     * different methods, like findByDuration(), findByEmail(), findBy...(), etc.
     *
     * Current solution not good enough. If we want to be able to apply filters like this,
     * it will be better make some abstract QueryParamBag class,
     * that can collect and validate params pairs and use as Iterable here.
     *
     * @param Builder $q
     * @param array $filters [['field1', '<', 12], ['field2', '=', 'abc']], etc.
     * @return Builder
     */
    protected function applyFilters(Builder $q, array $filters): Builder {

        //TODO: add filter params validation, rewrite current solution
        foreach ($filters as $filter) {
            $q->where($filter[0], $filter[1], $filter[2]);
        }

        return $q;
    }

    /**
     * Return last song entry for provided email.
     * Throw exception if nothing found.
     * @param string $email
     * @return mixed
     */
    protected function lastEmailEntry(string $email)
    {
        return $this->model::where('email', $email)
            ->latest('id')->firstOrFail();
    }
}
