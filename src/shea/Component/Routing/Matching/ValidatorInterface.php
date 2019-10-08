<?php

namespace Shea\Component\Routing\Matching;

use Shea\Component\Http\Request;
use Shea\Component\Routing\Route;

interface ValidatorInterface
{
    public function matches(Route $route, Request $request);
}
