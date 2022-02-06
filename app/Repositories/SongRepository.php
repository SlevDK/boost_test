<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class SongRepository
{
    protected $model;

    public function __construct(string $model_namespace)
    {
        $this->model = $model_namespace;
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
