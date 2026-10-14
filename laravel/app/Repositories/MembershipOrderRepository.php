<?php

namespace App\Repositories;

use App\Models\MembershipOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipOrderRepository
{
    /** @return HasMany<MembershipOrder, User> */
    public function forCustomer(User $customer): HasMany
    {
        return $customer->membershipOrders()
            ->with(['gymPackage', 'membership', 'payments'])
            ->withSum('payments', 'amount')
            ->latest();
    }

    public function findForCustomer(User $customer, int $orderId): MembershipOrder
    {
        return $customer->membershipOrders()
            ->with(['gymPackage', 'membership', 'payments.receiver'])
            ->findOrFail($orderId);
    }

    /** @return Builder<MembershipOrder> */
    public function forStaff(?string $search = null, ?string $status = null): Builder
    {
        return MembershipOrder::query()
            ->with(['user', 'gymPackage', 'payments.receiver', 'membership'])
            ->withSum('payments', 'amount')
            ->when($status !== null, fn (Builder $query): Builder => $query->where('status', $status))
            ->when($search !== null && $search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    if (ctype_digit($search)) {
                        $query->whereKey((int) $search);
                    }

                    $query->orWhereHas('user', function (Builder $query) use ($search): void {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%');
                    })->orWhereHas('gymPackage', fn (Builder $query): Builder => $query->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest();
    }

    /** @return Collection<int, MembershipOrder> */
    public function recent(int $limit): Collection
    {
        return MembershipOrder::query()
            ->with(['user', 'gymPackage'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function pendingCount(): int
    {
        return MembershipOrder::query()->where('status', MembershipOrder::STATUS_PENDING)->count();
    }

    public function paidRevenue(): int
    {
        return (int) MembershipOrder::query()
            ->where('status', MembershipOrder::STATUS_PAID)
            ->sum('amount');
    }

    /** @param array<string, mixed> $attributes */
    public function createPending(array $attributes): MembershipOrder
    {
        return MembershipOrder::query()->create($attributes);
    }

    public function lockForUpdate(int $orderId): MembershipOrder
    {
        return MembershipOrder::query()->lockForUpdate()->findOrFail($orderId);
    }
}
