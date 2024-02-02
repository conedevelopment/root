<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Models\Medium;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('signed');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Medium $medium): BinaryFileResponse
    {
        return $medium->download();
    }
}
