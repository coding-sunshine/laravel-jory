<?php

namespace JosKolenberg\LaravelJory\Blueprint;

use Illuminate\Contracts\Support\Responsable;

/**
 * Class Blueprint
 *
 * @package JosKolenberg\LaravelJory\Blueprint
 */
class Blueprint implements Responsable
{
    /**
     * @var null|array
     */
    protected $fields = null;

    /**
     * @var array
     */
    protected $filters = null;

    /**
     * @var array
     */
    protected $sorts = null;

    /**
     * @var null|int
     */
    protected $offset = null;

    /**
     * @var null|int
     */
    protected $limit = null;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var null|int
     */
    protected $limitDefault = null;

    /**
     * @var null|int
     */
    protected $limitMax = null;

    /**
     * Blueprint constructor.
     */
    public function __construct()
    {
        $this->limitDefault = config('jory.blueprint.limit.default');
        $this->limitMax = config('jory.blueprint.limit.max');
    }

    /**
     * Add a field to the blueprint.
     *
     * @param $field
     * @return Field
     */
    public function field($field): Field
    {
        $field = new Field($field);
        if ($this->fields === null) {
            $this->fields = [];
        }

        $this->fields[] = $field;

        return $field;
    }

    /**
     * Add a filter to the blueprint.
     *
     * @param $field
     * @return Filter
     */
    public function filter($field): Filter
    {
        $filter = new Filter($field);
        if ($this->filters === null) {
            $this->filters = [];
        }

        $this->filters[] = $filter;

        return $filter;
    }

    /**
     * Add a sort to the blueprint.
     *
     * @param $field
     * @return Sort
     */
    public function sort($field): Sort
    {
        $sort = new Sort($field);
        if ($this->sorts === null) {
            $this->sorts = [];
        }

        $this->sorts[] = $sort;

        return $sort;
    }

    /**
     * Set the default value for limit parameter.
     *
     * @param null|int $default
     * @return Blueprint
     */
    public function limitDefault(?int $default): self
    {
        $this->limitDefault = $default;

        return $this;
    }

    /**
     * Set the maximum value for limit parameter.
     *
     * @param null|int $max
     * @return Blueprint
     */
    public function limitMax(?int $max): self
    {
        $this->limitMax = $max;

        return $this;
    }

    /**
     * Get the fields in the blueprint.
     *
     * @return array|null
     */
    public function getFields(): ? array
    {
        return $this->fields;
    }

    /**
     * Get the filters in the blueprint.
     *
     * @return array|null
     */
    public function getFilters(): ? array
    {
        return $this->filters;
    }

    /**
     * Get the sorts in the blueprint.
     *
     * @return array|null
     */
    public function getSorts(): ? array
    {
        return $this->sorts;
    }

    /**
     * Get the default value for limit.
     *
     * @return null|int
     */
    public function getLimitDefault(): ? int
    {
        if($this->limitDefault !== null){
            return $this->limitDefault;
        }
        return $this->limitMax;
    }

    /**
     * Get the maximum value for limit.
     *
     * @return null|int
     */
    public function getLimitMax(): ? int
    {
        return $this->limitMax;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        return response($this->toArray());
    }

    /**
     * Convert the blueprint to an array to be shown when using OPTIONS.
     *
     * @return array
     */
    protected function toArray(): array
    {
        return [
            'fields' => $this->fieldsToArray(),
            'filters' => $this->filtersToArray(),
            'sorts' => $this->sortsToArray(),
            'limit' => [
                'default' => ($this->getLimitDefault() === null ? 'Unlimited.' : $this->getLimitDefault()),
                'max' => ($this->getLimitMax() === null ? 'Unlimited.' : $this->getLimitMax()),
            ],
        ];
    }

    /**
     * Turn the fields part of the blueprint into an array.
     *
     * @return array|string
     */
    protected function fieldsToArray()
    {
        if ($this->fields === null) {
            return 'Not defined.';
        }

        $result = [];
        foreach ($this->fields as $field) {
            $result[$field->getField()] = [
                'description' => $field->getDescription(),
                'show_by_default' => $field->isShownByDefault(),
            ];
        }

        return $result;
    }

    /**
     * Turn the filters part of the blueprint into an array.
     *
     * @return array|string
     */
    protected function filtersToArray()
    {
        if ($this->filters === null) {
            return 'Not defined.';
        }

        $result = [];
        foreach ($this->filters as $filter) {
            $result[$filter->getField()] = [
                'description' => $filter->getDescription(),
                'operators' => $filter->getOperators(),
            ];
        }

        return $result;
    }

    /**
     * Turn the sorts part of the blueprint into an array.
     *
     * @return array|string
     */
    protected function sortsToArray()
    {
        if ($this->sorts === null) {
            return 'Not defined.';
        }

        $result = [];
        foreach ($this->sorts as $sort) {
            $result[$sort->getField()] = [
                'description' => $sort->getDescription(),
            ];
        }

        return $result;
    }
}