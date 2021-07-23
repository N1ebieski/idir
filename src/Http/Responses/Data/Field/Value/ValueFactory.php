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
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists(string $type) : bool
    {
        return class_exists($this->className($type));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type) : string
    {
        return "\\N1ebieski\\IDir\\Http\\Responses\\Data\\Field\\Value\\Types\\" . ucfirst($type);
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @param GusReport $gusReport
     * @return Value
     */
    public function makeValue(string $type, GusReport $gusReport) : Value
    {
        if ($this->isClassExists($type)) {
            return $this->app->make($this->className($type), ['gusReport' => $gusReport]);
        }

        return $this->app->make(Gus::class, [
            'type' => $type,
            'gusReport' => $gusReport
        ]);
    }
}
