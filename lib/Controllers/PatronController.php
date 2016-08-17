<?php

namespace Guardian\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Guardian\Models\Patron;
use Guardian\Requests\Patron\UpdateRequest;
use Guardian\Requests\Patron\CreateRequest;
use Ramsey\Uuid\Uuid;

class PatronController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $patrons = Patron::all();
        $content = [
            'result' => 'success',
            'data' => $patrons,
        ];
        return response()->json($content);
    }

    public function store(CreateRequest $request) {
        try {
            $patron = Patron::create($request->getForm()->all());
            $patron->save();
            return $this->getCreatedResponse();
        } catch (\Exception $e) {
            $this->logger->error($e);
            return $this->getFailedResponse();
        }
    }

    /**
     * @param string $patron Patron ID
     * @param UpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($patron, UpdateRequest $request)
    {
        if (!Uuid::isValid($patron)) {
            return $this->getInvalidResponse();
        }

        try {
            $instance = Patron::findOrFail($patron);
        } catch (ModelNotFoundException $e) {
            return $this->getNotFoundResponse();
        }

        foreach ($request->getForm()->all() as $key => $value) {
            $instance->setAttribute($key, $value);
        }
        $instance->save();

        return $this->getSuccessResponse();
    }

    /**
     * @param string $patron Patron ID from Url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($patron)
    {
        if (!Uuid::isValid($patron)) {
            return response()->json(['result' => 'failed'])->setStatusCode(422);
        }

        Patron::destroy($patron);
        return $this->getSuccessResponse();
    }

}
