<?php


namespace JosKolenberg\LaravelJory\Traits;


use JosKolenberg\LaravelJory\Config\Config;

trait ConvertsConfigToArray
{

    /**
     * Convert a Config object to an array.
     *
     * @param Config $config
     * @return array
     */
    protected function configToArray(Config $config): array
    {
        return [
            'fields' => $this->fieldsToArray($config->getFields()),
            'filters' => $this->filtersToArray($config->getFilters()),
            'sorts' => $this->sortsToArray($config->getSorts()),
            'limit' => [
                'default' => $config->getLimitDefault(),
                'max' => $config->getLimitMax(),
            ],
            'relations' => $this->relationsToArray($config->getRelations()),
        ];
    }

    /**
     * Turn the fields part of the config into an array.
     *
     * @param array $fields
     * @return array
     */
    protected function fieldsToArray(array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[] = [
                'field' => $field->getField(),
                'description' => $field->getDescription(),
                'default' => $field->isShownByDefault(),
            ];
        }

        return $result;
    }

    /**
     * Turn the filters part of the config into an array.
     *
     * @param array $filters
     * @return array
     */
    protected function filtersToArray(array $filters): array
    {
        $result = [];
        foreach ($filters as $filter) {
            $result[] = [
                'field' => $filter->getField(),
                'description' => $filter->getDescription(),
                'operators' => $filter->getOperators(),
            ];
        }

        return $result;
    }

    /**
     * Turn the sorts part of the config into an array.
     *
     * @param array $sorts
     * @return array
     */
    protected function sortsToArray(array $sorts): array
    {
        $result = [];
        foreach ($sorts as $sort) {
            $result[] = [
                'field' => $sort->getField(),
                'description' => $sort->getDescription(),
                'default' => ($sort->getDefaultIndex() === null ? false : [
                    'index' => $sort->getDefaultIndex(),
                    'order' => $sort->getDefaultOrder(),
                ]),
            ];
        }

        return $result;
    }

    /**
     * Turn the relations part of the config into an array.
     *
     * @param array $relations
     * @return array|string
     */
    protected function relationsToArray(array $relations): array
    {
        $result = [];
        foreach ($relations as $relation) {
            $result[] = [
                'relation' => $relation->getName(),
                'description' => $relation->getDescription(),
                'type' => $relation->getType(),
            ];
        }

        return $result;
    }
}