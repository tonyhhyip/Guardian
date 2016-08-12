<?php

namespace Guardian\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Log\Writer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $logger;

    public function __construct(Writer $logger)
    {
        $this->logger = $logger;
    }

    protected function getSuccessResponse()
    {
        return response()->json(['result' => 'success']);
    }

    protected function getInvalidResponse()
    {
        return response()->json(['result' => 'failed'], 422);
    }

    protected function getCreatedResponse()
    {
        return response()->json(['result' => 'success'], 201);
    }

    protected function getFailedResponse()
    {
        return response()->json(['result' => 'failed'], 500);
    }

    protected function getNotFoundResponse()
    {
        return response()->json(['result' => 'failed'], 404);
    }
}