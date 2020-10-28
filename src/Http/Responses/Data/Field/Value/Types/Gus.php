<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field\Value\Types;

use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Value;

class Gus extends Value
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $type;

    /**
     * Undocumented function
     *
     * @param string $type
     * @param GusReport $gusReport
     */
    public function __construct(string $type, GusReport $gusReport)
    {
        parent::__construct($gusReport);

        $this->type = $type;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isMethodExists() : bool
    {
        return method_exists($this->gusReport, $this->methodName());
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function methodName() : string
    {
        return 'get' . ucfirst($this->type);
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function __invoke() : ?string
    {
        if ($this->isMethodExists()) {
            $method = $this->methodName();

            return $this->gusReport->$method();
        }

        return null;
    }
}
