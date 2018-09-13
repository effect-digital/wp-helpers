<?php

namespace Industrious\WpHelpers\Traits;

use Industrious\WpHelpers\Validation\Factory as ValidatorFactory;

trait ValidatesRequests
{
    /**
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages     
     * @return mixed
     */
    public function validate(array $data, array $rules, array $messages = [])
    {
        $factory = new ValidatorFactory;

        $validator = $factory->make($data, $rules, $messages);

        if ($validator->passes()) {
            return false;
        }

        return $validator->errors();
    }
}
