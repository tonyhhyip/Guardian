<?php

namespace Guardian\Controllers;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Guardian\Models\Branch;
use Guardian\Requests\Branch\UpdateRequest;
use Guardian\Requests\Branch\CreateRequest;

class BranchController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $branches = Branch::all();
        $content = [
            'result' => 'success',
            'data' => $branches,
        ];
        return response()->json($content);
    }

    /**
     * @param CreateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request) {
        try {
            $branch = Branch::create($request->getForm()->all());
            $branch->save();
            return $this->getCreatedResponse();
        } catch (\PDOException $e) {
            $this->logger->error($e);
            return $this->getFailedResponse();
        }
    }

    /**
     * @param string $branch Branch ID
     * @param UpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($branch, UpdateRequest $request)
    {
        try {
            if (!Uuid::isValid($branch)) {
                return $this->getInvalidResponse();
            }

            $instance = Branch::findOrFail($branch);
            foreach ($request->getForm()->all() as $key => $value) {
                $instance->setAttribute($key, $value);
            }
            $instance->save();
            return $this->getSuccessResponse();
        } catch (ModelNotFoundException $e) {
            $this->logger->error($e);
            return $this->getNotFoundResponse();
        }
    }

    /**
     * @param string $branch Branch ID from Url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($branch)
    {
        try {
            if (!Uuid::isValid($branch)) {
                return $this->getInvalidResponse();
            }

            Branch::findOrFail($branch)->delete();
            return $this->getSuccessResponse();
        } catch (ModelNotFoundException $e) {
            $this->logger->error($e);
            return $this->getNotFoundResponse();
        }
    }

}
