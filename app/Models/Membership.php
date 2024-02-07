<?php

namespace App\Models;

use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Membership extends JetstreamMembership
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
