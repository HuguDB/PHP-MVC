<?php

namespace App\Routing\Attribute;

use App\Routing\AbstractRoute;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AuthenticatedAttribute extends AbstractRoute
{
}
