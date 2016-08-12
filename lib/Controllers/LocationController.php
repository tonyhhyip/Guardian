<?php

namespace Guardian\Controllers;

use Guardian\Models\Location;
use Guardian\Requests\Location\CreateRequest;
use Guardian\Requests\Location\UpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;

class LocationController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $locations = Location::all();
        return response()->json(['result' => 'success', 'data' => $locations]);
    }

    public function store(CreateRequest $request)
    {
        try {
            $location = Location::create($request->getForm()->all());
            $location->save();
            return $this->getCreatedResponse();
        } catch (\PDOException $e) {
            $this->logger->error($e);
            return $this->getFailedResponse();
        }
    }

    public function update($location, UpdateRequest $request)
    {
        try {
            if (!Uuid::isValid($location))
                return $this->getInvalidResponse();

            $location = Location::firstOrFail($location);
            foreach ($request->getForm()->all() as $key => $value) {
                $location->setAttribute($key, $value);
            }
            $location->save();
            return $this->getSuccessResponse();
        } catch (ModelNotFoundException $e) {
            $this->logger->error($e);
            return $this->getNotFoundResponse();
        }
    }

    public function destroy($location)
    {
        if (!Uuid::isValid($location))
            return $this->getInvalidResponse();
        Location::destroy($location);
        return $this->getSuccessResponse();
    }
}