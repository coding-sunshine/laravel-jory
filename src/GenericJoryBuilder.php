<?php

namespace JosKolenberg\LaravelJory;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Parsers\JsonParser;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\LaravelJory\Contracts\JoryBuilderInterface;
use JosKolenberg\LaravelJory\Parsers\RequestParser;

/**
 * Class to query models based on Jory data.
 *
 * Class GenericJoryBuilder
 */
class GenericJoryBuilder implements JoryBuilderInterface, Responsable
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Jory
     */
    protected $jory;

    /**
     * GenericJoryBuilder constructor.
     */
    public function __construct()
    {
        // Set to empty jory by default in case none is applied.
        $this->jory = new Jory();
    }

    /**
     * Set a builder instance to build the query upon.
     *
     * @param Builder $builder
     *
     * @return GenericJoryBuilder
     */
    public function onQuery(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Set a Model to build the query upon.
     *
     * @param Model|string $class
     *
     * @return GenericJoryBuilder
     */
    public function onModel($class): self
    {
        if ($class instanceof Model) {
            $this->onQuery($class->newQuery());
        } else {
            $this->onQuery((new $class())->newQuery());
        }

        return $this;
    }

    /**
     * Apply an array with Jory data.
     *
     * @param array $array
     *
     * @return GenericJoryBuilder
     */
    public function applyArray(array $array): self
    {
        return $this->applyJory((new ArrayParser($array))->getJory());
    }

    /**
     * Apply a Json string with Jory data.
     *
     * @param string $json
     *
     * @return GenericJoryBuilder
     */
    public function applyJson(string $json): self
    {
        return $this->applyJory((new JsonParser($json))->getJory());
    }

    /**
     * Apply a request with Jory data.
     *
     * @param Request $request
     *
     * @return GenericJoryBuilder
     */
    public function applyRequest(Request $request): self
    {
        return $this->applyJory((new RequestParser($request))->getJory());
    }

    /**
     * Apply a Jory object.
     *
     * @param Jory $jory
     *
     * @return GenericJoryBuilder
     */
    public function applyJory(Jory $jory): self
    {
        $this->jory = $jory;

        return $this;
    }

    /**
     * Get a collection of Models based on the baseQuery and Jory data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(): Collection
    {
        return $this->getQuery()->get();
    }

    /**
     * Get the Laravel QueryBuilder based on the baseQuery and Jory data.
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->buildQuery();
    }

    /**
     * Build a new query based on the baseQuery and Jory data.
     *
     * @return Builder
     */
    protected function buildQuery(): Builder
    {
        $query = clone $this->builder;

        // Apply filters if there are any
        if ($this->jory->getFilter()) {
            $this->applyFilter($query, $this->jory->getFilter());
        }

        return $query;
    }

    /**
     * Apply a filter (field, groupAnd or groupOr) on a query.
     *
     * @param Builder $query
     * @param FilterInterface $filter
     */
    protected function applyFilter(Builder $query, FilterInterface $filter): void
    {
        if ($filter instanceof Filter) {
            $this->applyFieldFilter($query, $filter);
        }
        if ($filter instanceof GroupAndFilter) {
            $query->where(function ($query) use ($filter) {
                foreach ($filter as $subFilter) {
                    $this->applyFilter($query, $subFilter);
                }
            });
        }
        if ($filter instanceof GroupOrFilter) {
            $query->where(function ($query) use ($filter) {
                foreach ($filter as $subFilter) {
                    $query->orWhere(function ($query) use ($subFilter) {
                        $this->applyFilter($query, $subFilter);
                    });
                }
            });
        }
    }

    /**
     * Apply a filter to a field.
     *
     * @param Builder $query
     * @param Filter $filter
     */
    protected function applyFieldFilter(Builder $query, Filter $filter): void
    {
        switch ($filter->getOperator()) {
            case 'null':
                $query->whereNull($filter->getField());

                return;
            case 'not_null':
                $query->whereNotNull($filter->getField());

                return;
            case 'in':
                $query->whereIn($filter->getField(), $filter->getValue());

                return;
            case 'not_in':
                $query->whereNotIn($filter->getField(), $filter->getValue());

                return;
            default:
                $query->where($filter->getField(), $filter->getOperator() ?: '=', $filter->getValue());
        }
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response($this->get());
    }
}
