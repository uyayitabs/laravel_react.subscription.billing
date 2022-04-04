<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseApiRequest extends FormRequest
{
    public bool $isPut = false;
    public bool $isPost = false;
    public string $requiredOrNullable = 'nullable';

    public function __construct()
    {
        $this->isPut = request()->isMethod("patch") || request()->isMethod("put");
        $this->isPost = request()->isMethod("post");
        if ($this->isPut) {
            $this->requiredOrNullable = 'nullable';
        } elseif ($this->isPost) {
            $this->requiredOrNullable = 'required';
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    function offsetExists($offset): bool
    {
        return parent::offsetExists($offset);
    }

    /**
     * Get the value at the given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    function offsetGet($offset): mixed
    {
        return parent::offsetGet($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed $value
     *
     * @return void
     */
    function offsetSet($offset, $value): void
    {
        parent::offsetSet($offset, $value);
    }

    /**
     * Remove the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    function offsetUnset($offset): void
    {
        parent::offsetUnset($offset);
    }
}
