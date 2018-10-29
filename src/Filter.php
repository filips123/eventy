<?php

namespace EventyClassic;

class Filter extends Event
{
    /**
     * Holds the value of the filter.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Filters a value.
     *
     * When a filter is fired, all listeners are run in the order supplied when adding them.
     *
     * @param  string $action Name of filter.
     * @param  array  $args   Arguments passed to the filter.
     *
     * @return string         Always returns the value.
     */
    public function fire($action, $args)
    {
        $this->value = isset($args[0]) ? $args[0] : '';

        if ($this->getListeners()) {
            $listeners = $this->getListeners();

            foreach ($listeners as $listener) {
                if ($listener['hook'] == $action) {
                    $parameters = [];
                    $args[0] = $this->value;

                    for ($i = 0; $i < $listener['arguments']; $i++) {
                        if (isset($args[$i])) {
                            $parameters[] = $args[$i];
                        }
                    }

                    $this->value = call_user_func_array($this->getFunction($listener['callback']), $parameters);
                }
            }
        }

        return $this->value;
    }
}
