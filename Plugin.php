<?php namespace Akravets\Survey;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Akravets\Survey\Components\ShowQnA' => 'ShowQnA'
        ];
    }

    public function registerSettings()
    {
    }
}
