<?php

namespace Modules\Order\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    /**
     * Whether this status may change to the given target.
     *
     * Same status is always allowed (no-op save). Any other transition
     * must follow the next step in the workflow, except from Delivered.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::Pending => $target === self::Confirmed,
            self::Confirmed => $target === self::Shipped,
            self::Shipped => $target === self::Delivered,
            self::Delivered => false,
        };
    }

    /**
     * Status values selectable in admin UI and valid for persistence.
     *
     * Includes the current status so the form can save without changing it.
     *
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Pending, self::Confirmed],
            self::Confirmed => [self::Confirmed, self::Shipped],
            self::Shipped => [self::Shipped, self::Delivered],
            self::Delivered => [self::Delivered],
        };
    }
}
