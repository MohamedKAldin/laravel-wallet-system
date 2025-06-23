<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function acceptTopup(Admin $admin)
    {
        return is_array($admin->permissions) && in_array('can_accept_topup', $admin->permissions);
    }

    public function rejectTopup(Admin $admin)
    {
        return is_array($admin->permissions) && in_array('can_reject_topup', $admin->permissions);
    }

    public function acceptWithdrawals(Admin $admin)
    {
        return is_array($admin->permissions) && in_array('can_accept_withdrawals', $admin->permissions);
    }

    public function rejectWithdrawals(Admin $admin)
    {
        return is_array($admin->permissions) && in_array('can_reject_withdrawals', $admin->permissions);
    }
} 