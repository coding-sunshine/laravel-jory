<?php


namespace JosKolenberg\LaravelJory\Responses;


use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Parsers\JsonParser;
use JosKolenberg\LaravelJory\Exceptions;
use JosKolenberg\LaravelJory\Exceptions\LaravelJoryException;
use JosKolenberg\LaravelJory\JoryBuilder;
use JosKolenberg\LaravelJory\Parsers\RequestParser;
use JosKolenberg\LaravelJory\Register\JoryBuilderRegistration;
use JosKolenberg\LaravelJory\Register\JoryBuildersRegister;

/**
 * Class JoryResponse
 *
 * Class to turn jory requests into responses.
 */
class JoryResponse implements Responsable
{

    /**
     * @var JoryBuildersRegister
     */
    protected $register;

    /**
     * @var JoryBuilderRegistration
     */
    protected $registration;

    /**
     * @var JoryParserInterface
     */
    protected $parser;

    /**
     * @var bool
     */
    protected $count = false;

    /**
     * @var mixed
     */
    protected $modelId;

    /**
     * @var bool
     */
    protected $first = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * JoryResponse constructor.
     * @param Request $request
     * @param JoryBuildersRegister $register
     */
    public function __construct(Request $request, JoryBuildersRegister $register)
    {
        $this->register = $register;
        $this->request = $request;
    }

    /**
     * Set the resource to be called based on the uri.
     *
     * @param string $uri
     * @return $this
     * @throws Exceptions\ResourceNotFoundException
     */
    public function byUri(string $uri): JoryResponse
    {
        $this->registration = $this->register->getByUri($uri);

        return $this;
    }

    /**
     * Set the resource to be called based on the model class.
     *
     * @param string $modelClass
     * @return $this
     * @throws Exceptions\RegistrationNotFoundException
     */
    public function onModelClass(string $modelClass): JoryResponse
    {
        $this->registration = $this->register->getByModelClass($modelClass);

        return $this;
    }

    /**
     * Set the resource to be called based on the model class.
     *
     * @param Model $model
     * @return $this
     * @throws Exceptions\RegistrationNotFoundException
     */
    public function onModel(Model $model): JoryResponse
    {
        $this->registration = $this->register->getByModelClass(get_class($model));
        $this->find($model->getKey());

        return $this;
    }

    /**
     * Set the resource to be called based on the model class.
     *
     * @param Model $model
     * @return $this
     * @throws Exceptions\RegistrationNotFoundException
     */
    public function onQuery(Builder $query): JoryResponse
    {
        $this->registration = $this->register->getByModelClass(get_class($query->getModel()));
        $this->query = $query;

        return $this;
    }

    public function apply($jory)
    {
        if(is_array($jory)){
            return $this->applyArray($jory);
        }

        if(!is_string($jory)){
            throw new LaravelJoryException('Unexpected type given. Please provide an array or Json string.');
        }

        return $this->applyJson($jory);
    }

    /**
     * Manually apply a Json Jory string to the request.
     *
     * @param string $json
     * @return JoryResponse
     */
    public function applyJson(string $json): JoryResponse
    {
        $this->parser = new JsonParser($json);

        return $this;
    }

    /**
     * Manually apply a Jory array to the request.
     *
     * @param array $array
     * @return JoryResponse
     */
    public function applyArray(array $array): JoryResponse
    {
        $this->parser = new ArrayParser($array);

        return $this;
    }

    /**
     * Set the response to return the record count.
     *
     * @return $this
     */
    public function count(): JoryResponse
    {
        $this->count = true;

        return $this;
    }

    /**
     * Set the response to return a single model by the given id.
     *
     * @param $modelId
     * @return $this
     */
    public function find($modelId): JoryResponse
    {
        $this->modelId = $modelId;
        $this->first();

        return $this;
    }

    /**
     * Set the response to return only a single model.
     *
     * @return $this
     */
    public function first(): JoryResponse
    {
        $this->first = true;

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return void
     * @throws Exceptions\LaravelJoryCallException
     * @throws Exceptions\LaravelJoryException
     * @throws JoryException
     */
    public function toResponse($request)
    {
        $data = $this->toArray();

        $responseKey = $this->getDataResponseKey();
        $response = $responseKey === null ? $data : [$responseKey => $data];

        return response($response);
    }

    /**
     * Get the result of the request.
     *
     * @return mixed
     * @throws Exceptions\LaravelJoryCallException
     * @throws Exceptions\LaravelJoryException
     * @throws JoryException
     */
    public function toArray()
    {
        $builder = $this->getProcessedBuilder();

        if($this->count){
            return $builder->getCount();
        }

        if($this->first){
            return $builder->firstToArray();
        }

        return $builder->toArray();
    }

    /**
     * Get the JoryBuilder with everything applied.
     *
     * @return JoryBuilder
     * @throws Exceptions\LaravelJoryCallException
     * @throws Exceptions\LaravelJoryException
     * @throws JoryException
     */
    protected function getProcessedBuilder(): JoryBuilder
    {
        $builder = $this->getBuilder();

        $builder->onQuery($this->getBaseQuery());

        $builder->applyJory($this->getJory());

        $builder->validate();

        return $builder;
    }

    /**
     * Get the key on which data should be returned.
     *
     * @return null|string
     */
    protected function getDataResponseKey(): ?string
    {
        return config('jory.response.data-key');
    }

    /**
     * @return JoryBuilder
     * @throws Exceptions\LaravelJoryException
     */
    protected function getBuilder(): JoryBuilder
    {
        if(!$this->registration){
            throw new Exceptions\LaravelJoryException('No resource has been set on the JoryResponse. Use the on() method to set a resource.');
        }
        $builderClass = $this->registration->getBuilderClass();
        return new $builderClass();
    }

    /**
     * Get the base query to apply the jory on.
     *
     * @return mixed
     */
    protected function getBaseQuery()
    {
        if($this->query){
            return $this->query;
        }

        $modelClass = $this->registration->getModelClass();

        $query = $modelClass::query();

        if($this->modelId !== null){
            $query->whereKey($this->modelId);
        }

        return $query;
    }

    /**
     * Get the Jory object to be applied
     *
     * @return Jory
     * @throws JoryException
     */
    protected function getJory(): Jory
    {
        /**
         * If a parser is set return the Jory from this parser,
         * otherwise default to the data in the request.
         */
        if($this->parser){
            return $this->parser->getJory();
        }

        return (new RequestParser($this->request))->getJory();
    }
}