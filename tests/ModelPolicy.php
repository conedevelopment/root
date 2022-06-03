<?php

namespace Cone\Root\Tests;

use Cone\Root\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can perform any actions.
     *
     * @param  \Bazar\Models\User  $user
     * @param  string  $ability
     * @return mixed
     */
    public function before(User $user, string $ability)
    {
        return true;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Cone\Root\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Cone\Root\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function view(User $user, Model $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Cone\Root\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Cone\Root\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function update(User $user, Model $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Cone\Root\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function delete(User $user, Model $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Cone\Root\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function restore(User $user, Model $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Cone\Root\Models\User  $user
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function forceDelete(User $user, Model $model)
    {
        //
    }
}
