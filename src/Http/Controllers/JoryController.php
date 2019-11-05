<?php

namespace JosKolenberg\LaravelJory\Http\Controllers;

use Illuminate\Routing\Controller;
use JosKolenberg\LaravelJory\Facades\Jory;
use JosKolenberg\LaravelJory\Responses\JoryResponse;

class JoryController extends Controller
{
    /**
     * Load a collection for a single resource.
     *
     * @param string $resource
     * @return JoryResponse
     */
    public function index(string $resource)
    {
        return Jory::byUri($resource);
    }

    /**
     * Count the number of items in a resource.
     *
     * @param string $resource
     * @return JoryResponse
     */
    public function count(string $resource)
    {
        return Jory::byUri($resource)->count();
    }

    /**
     * Give a single record by id.
     *
     * @param string $resource
     * @param $id
     * @return JoryResponse
     */
    public function show(string $resource, $id)
    {
        return Jory::byUri($resource)->find($id);
    }

    /**
     * Give the first record by filter and sort parameters.
     *
     * @param string $resource
     * @return JoryResponse
     */
    public function first(string $resource)
    {
        return Jory::byUri($resource)->first();
    }

    /**
     * Load multiple resources at once.
     *
     */
    public function multiple()
    {
        return Jory::multiple();
    }
}
