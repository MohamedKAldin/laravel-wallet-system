<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Transaction $transaction): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return $user->id === $transaction->wallet->owner_id && $transaction->wallet->owner_type === get_class($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Transaction $transaction): bool
    {
        if ($user instanceof Admin) {
            if ($transaction->wallet->owner_id === $user->id && $transaction->wallet->owner_type === get_class($user)) {
                return false;
            }

            if ($transaction->type === 'top-up') {
                return in_array('can_accept_topup', $user->permissions) || in_array('can_reject_topup', $user->permissions);
            }

            if ($transaction->type === 'withdrawal') {
                return in_array('can_accept_withdrawals', $user->permissions) || in_array('can_reject_withdrawals', $user->permissions);
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        //
    }
}
