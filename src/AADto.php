<?php
class AADto implements ArrayAccess
{

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->data[] = $value;
        }
        else {
            $this->data[$key] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

}

// This gets treaded exactly as an array
$dto = new AADto();

$dto->hobbies[] = 'cooking';
print_r($dto);
