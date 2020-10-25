<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field\Value;

use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Gus;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Value;

class ValueFactory
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $type;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var GusReport
     */
    protected $gusReport;

    /**
     * Undocumented function
     *
     * @param string $type
     * @param App $app
     * @param GusReport $gusReport
     */
    public function __construct(string $type, App $app, GusReport $gusReport)
    {
        $this->app = $app;

        $this->gusReport = $gusReport;

        $this->type = $type;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists() : bool
    {
        return class_exists($this->className());
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className() : string
    {
        return "\\N1ebieski\\IDir\\Http\\Responses\\Data\\Field\\Value\\Types\\" . ucfirst($this->type);
    }

    /**
     * Undocumented function
     *
     * @return Value
     */
    public function makeValue() : Value
    {
        if ($this->isClassExists()) {
            return $this->app->make($this->className(), ['gusReport' => $this->gusReport]);
        }

        return $this->app->make(Gus::class, [
            'type' => $this->type,
            'gusReport' => $this->gusReport
        ]);
    }
}
