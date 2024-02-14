<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileTypeValidate implements Rule
{
    protected $extensions;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($extensions)
    {

        $this->extensions = $extensions;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     */
    public function passes(string $attribute, $value): bool
    {
        return in_array($value->getClientOriginalExtension(), $this->extensions);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return ':attribute file type is not supported.';
    }
}
