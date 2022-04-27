<?php

namespace N1ebieski\IDir\Console\Commands\Update;

use N1ebieski\ICore\Console\Commands\Update\SchemaFactory as BaseSchemaFactory;

class SchemaFactory extends BaseSchemaFactory
{
    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type): string
    {
        return "N1ebieski\\IDir\\Utils\\Updater\\Schema\\Schema" . ucfirst($type);
    }
}
