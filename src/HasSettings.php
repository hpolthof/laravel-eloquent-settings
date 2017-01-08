<?php namespace Hpolthof\LaravelEloquentSettings;

use Illuminate\Support\Collection;

trait HasSettings
{
    protected $__attributes = null;
    protected $settingsField = 'settings';

    public function getSettingsAttribute($value) {
        if($this->__attributes === null) {
            $this->__attributes = [];
            if(strlen($value) > 0) {
                $this->__attributes = json_decode($value, true);
            }
        }
        return $this->__attributes;
    }

    public function setSettingsAttribute($value) {
        $this->attributes[$this->settingsField] = json_encode($value);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getSetting($name, $default = null) {
        if($this->hasSetting($name)) {
            return $this->{$this->settingsField}[$name];
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
        $this->{$this->settingsField} = $this->__attributes;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSetting($name) {
        return array_key_exists($name, $this->{$this->settingsField});
    }

    /**
     * @return Collection
     */
    public function getSettingsCollection()
    {
        return new Collection($this->{$this->settingsField});
    }
}