<?php
namespace Santander;

trait TraitEntity
{

    public function jsonSerialize()
    {
        $entity = clone $this;

        if (method_exists($entity, 'beforeSerialize')) {
            $entity->beforeSerialize();
        }

        return $entity->toArray();
    }
        
    /**
     *
     * @return array
     */
    public function toArray()
    {
        $vars = get_object_vars($this);

        if ($this->hiddenNullValues()) {
            return array_filter($vars, function ($value) {
                return null !== $value;
            });
        }

        return $vars;
    }

    /**
     *
     * @return false|string
     */
    public function toJSON($hiddenNull = true)
    {
        if ($hiddenNull) {
            return json_encode($this);
        }

        return json_encode(get_object_vars($this));
    }

    public function populateByArray(array $body, array $blockFields = [])
    {
        foreach ($body as $prop => $value) {
            if (property_exists($this, $prop) && null !== $value && !in_array($prop, $blockFields)) {
                $this->{$prop} = $value;
            }
        }
        
        return $this;
    }

    private function hiddenNullValues(): bool
    {
        return true;
    }
}