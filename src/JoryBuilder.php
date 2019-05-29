<?php

namespace JosKolenberg\LaravelJory;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Jory;
use JosKolenberg\LaravelJory\Config\Validator;
use JosKolenberg\LaravelJory\Exceptions\LaravelJoryCallException;
use JosKolenberg\LaravelJory\Exceptions\LaravelJoryException;
use JosKolenberg\LaravelJory\Helpers\CaseManager;
use JosKolenberg\LaravelJory\Traits\ConvertsModelToArrayByJory;
use JosKolenberg\LaravelJory\Traits\HandlesJoryBuilderConfiguration;
use JosKolenberg\LaravelJory\Traits\HandlesJoryFilters;
use JosKolenberg\LaravelJory\Traits\HandlesJorySorts;
use JosKolenberg\LaravelJory\Traits\LoadsJoryRelations;

/**
 * Class to query models based on Jory data.
 *
 * Class JoryBuilder
 */
class JoryBuilder
{
    use HandlesJorySorts,
        HandlesJoryFilters,
        LoadsJoryRelations,
        ConvertsModelToArrayByJory;

    /**
     * @var Builder
     */
    protected $joryResource;

    /**
     * @var null|Jory
     */
    protected $jory = null;

    /**
     * @var CaseManager
     */
    protected $case = null;

    public function __construct(JoryResource $joryResource)
    {
        $this->joryResource = $joryResource;
        $this->jory = $joryResource->getJory();

        $this->case = app(CaseManager::class);
    }

    /**
     * Set a builder instance to build the query upon.
     *
     * @param Builder $builder
     *
     * @return JoryBuilder
     */
    public function onQuery(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Get a collection of Models based on the baseQuery and Jory data.
     *
     * @return Collection
     * @throws LaravelJoryException
     * @throws JoryException
     */
    protected function get(): Collection
    {
        $collection = $this->buildQuery()->get();

        $jory = $this->jory;
        $collection = $this->joryResource->afterFetch($collection);

        $this->loadRelations($collection, $jory->getRelations());

        return $collection;
    }

    /**
     * Get the first Model based on the baseQuery and Jory data.
     *
     * @return Model|null
     * @throws JoryException
     * @throws LaravelJoryException
     */
    public function getFirst(): ?Model
    {
        $model = $this->buildQuery()->first();

        if (!$model) {
            return null;
        }

        $model = $this->joryResource->afterFetch(new Collection([$model]))->first();

        $this->loadRelations(new Collection([$model]), $this->jory->getRelations());

        return $model;
    }

    /**
     * Count the records based on the filters in the Jory object.
     *
     * @return int
     * @throws LaravelJoryException
     * @throws JoryException
     */
    public function getCount(): int
    {
        $query = clone $this->builder;

        $jory = $this->jory;

        $this->joryResource->beforeQueryBuild($query, $jory, true);

        // Apply filters if there are any
        if ($jory->getFilter()) {
            $this->applyFilter($query, $jory->getFilter());
        }

        $this->joryResource->afterQueryBuild($query, $jory, true);

        return $query->count();
    }

    /**
     * Get the result array for the first result.
     *
     * @return array|null
     * @throws LaravelJoryException
     * @throws JoryException
     */
    public function firstToArray(): ?array
    {
        $model = $this->getFirst();
        if (!$model) {
            return null;
        }

        return $this->modelToArray($model);
    }

    /**
     * Get the result array.
     *
     * @return array
     * @throws LaravelJoryException
     * @throws JoryException
     */
    public function toArray(): array
    {
        $models = $this->get();

        $result = [];
        foreach ($models as $model) {
            $result[] = $this->modelToArray($model);
        }

        return $result;
    }

    /**
     * Build a new query based on the baseQuery and Jory data.
     *
     * @return Builder
     * @throws LaravelJoryException
     * @throws JoryException
     */
    protected function buildQuery(): Builder
    {
        $query = $this->builder;

        $this->applyOnQuery($query);

        return $query;
    }

    /**
     * Apply the jory data on an existing query.
     *
     * @param $query
     * @throws LaravelJoryException
     * @throws JoryException
     */
    public function applyOnQuery($query): void
    {
        $jory = $this->jory;
        $this->joryResource->beforeQueryBuild($query, $jory);

        // Apply filters if there are any
        if ($jory->getFilter()) {
            $this->applyFilter($query, $jory->getFilter());
        }

        $this->applySorts($query, $jory->getSorts());
        $this->applyOffsetAndLimit($query, $jory->getOffset(), $jory->getLimit());

        $this->joryResource->afterQueryBuild($query, $jory);
    }

    /**
     * Apply an offset and limit on the query.
     *
     * @param $query
     * @param int|null $offset
     * @param int|null $limit
     */
    protected function applyOffsetAndLimit($query, int $offset = null, int $limit = null): void
    {
        if ($offset !== null) {
            // Check on null, so even 0 will be applied.
            // In case a default is set in beforeQueryBuild()
            // this can be overruled by the request this way.
            $query->offset($offset);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
    }

    /**
     * Convert a single model to an array based on the request in the Jory object.
     *
     * @param Model $model
     * @return array
     * @throws LaravelJoryException
     * @throws JoryException
     */
    public function modelToArray(Model $model): array
    {
        return $this->modelToArrayByJory($model, $this->joryResource);
    }
}
