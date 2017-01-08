<?php namespace Hpolthof\LaravelEloquentSettings;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasSettings
{
    protected $__attributes = null;
    protected $__settingsWithDefaults = null;

    public function __call($method, $parameters)
    {
        if($method == 'get'.Str::studly($this->getSettingsFieldName()).'Attribute') {
            return call_user_func([$this, '__getSettingsAttribute'], $parameters[0]);
        }

        if($method == 'set'.Str::studly($this->getSettingsFieldName()).'Attribute') {
            return call_user_func([$this, '__setSettingsAttribute'], $parameters[0]);
        }

        return parent::__call($method, $parameters);
    }

    public function hasGetMutator($key)
    {
        if($key === $this->getSettingsFieldName()) {
            return true;
        }
        return parent::hasGetMutator($key);
    }

    public function hasSetMutator($key)
    {
        if($key === $this->getSettingsFieldName()) {
            return true;
        }
        return parent::hasSetMutator($key);
    }

    private function __getSettingsAttribute($value) {
        if($this->__attributes === null) {
            $this->__attributes = [];
            if(strlen($value) > 0) {
                $this->__attributes = json_decode($value, true);
            }
        }
        return $this->__attributes;
    }

    private function __setSettingsAttribute($value) {
        $this->attributes[$this->getSettingsFieldName()] = json_encode($value);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getSetting($name, $default = null) {
        if($this->hasSetting($name)) {
            return $this->__settingsWithDefaults[$name];
        }
        return $default;
    }

    /**
     * @param $name
     * @param $value
     * @return static
     */
    public function setSetting($name, $value) {
        $this->hasSetting($name);
        $this->__attributes[$name] = $value;
        $this->{$this->getSettingsFieldName()} = $this->__attributes;
        $this->populateSettingsArray();
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSetting($name) {
        $this->populateSettingsArray();
        return array_key_exists($name, $this->__settingsWithDefaults);
    }

    /**
     * @return Collection
     */
    public function getSettingsCollection()
    {
        $this->populateSettingsArray();
        return new Collection($this->__settingsWithDefaults);
    }

    /**
     * @return array
     */
    protected function mergeDefaultSettings()
    {
        $flatSettings = array_dot($this->{$this->getSettingsFieldName()});
        $flatDefaults = array_dot($this->getDefaultSettingsArray());

        foreach($flatDefaults as $k => $v) {
            $flatSettings[$k] = $v;
        }

        $result = [];
        foreach($flatSettings as $k => $v) {
            array_set($result, $k, $v);
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function getSettingsArray()
    {
        if(count($this->getDefaultSettingsArray()) > 0) {
            return $this->mergeDefaultSettings();
        }
        return $this->{$this->getSettingsFieldName()};
    }

    /**
     * @return $this
     */
    protected function populateSettingsArray()
    {
        $this->__settingsWithDefaults = $this->getSettingsArray();
        return $this;
    }

    private function getSettingsFieldName()
    {
        if(property_exists($this, 'settingsField')) {
            return $this->settingsField;
        }
        return 'settings';
    }

    private function getDefaultSettingsArray()
    {
        if(property_exists($this, 'defaultSettings')) {
            return $this->defaultSettings;
        }
        return [];
    }
}