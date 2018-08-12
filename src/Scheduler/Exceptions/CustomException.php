<?php namespace H4ad\Scheduler\Exceptions;

/**
 * Esse arquivo faz parte do Scheduler,
 * uma biblioteca para auxiliar com agendamentos.
 *
 * @license MIT
 * @package H4ad\Scheduler
 */

class CustomException extends \Exception
{
    /**
     * Model que não pertence ao usuário
     *
     * @var mixed
     */
    protected $model;

    /**
     * Key para o arquivo de tradução de exceções.
     *
     * @var string
     */
    protected $trans;

    /**
     * HTTP Status Code
     *
     * @var int
     */
    protected $statusCode;

    /**
     * Atributo usado como key para substituir por um texto.
     *
     * @var array|string
     */
    protected $attributes = 'model';

    /**
     * Diz se o alias será no singular ou no plural.
     *
     * @var array
     */
    protected $aliastype = 'singular';

    /**
     * Valor passado para o atributo
     *
     * @var array|string|null
     */
    protected $values = null;

    /**
     * Indica se a model será printada com lower case.
     *
     * @var boolean
     */
    protected $lowercase = false;

    /**
     * Construtor da exceção
     *
     * @param mixed $model
     */
    public function __construct($model = 'foo')
    {
        $this->model = $model;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json([
            'messages' => trans('scheduler::exceptions.'. $this->trans, $this->parseValues())
        ], $this->statusCode);
    }

    /**
     * Dá parse nos valores para a string de tradução.
     *
     * @return array|string
     */
    protected function parseValues()
    {
        if(is_array($this->attributes) && is_array($this->values))
            return collect($this->attributes)->combine($this->values)->all();

        return [ $this->attributes => $this->values ?? $this->isLower() ];
    }

    /**
     * Verifica se é lowercase e retorna de acordo.
     *
     * @return string
     */
    protected function isLower()
    {
        return $this->lowercase ? strtolower($this->getAlias()) : $this->getAlias();
    }

    /**
     * Retorna o alias da model.
     *
     * @return string
     */
    protected function getAlias()
    {
        if(is_object($this->model))
            $this->model = get_class($this->model);

        return collect(trans('scheduler::exceptions.aliases.'. $this->aliastype))->search($this->model) ?: 'Recurso';
    }

    /**
     * Seta os valores.
     *
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }
}
