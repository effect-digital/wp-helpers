<?php

namespace Industrious\WpHelpers\Traits;

use Industrious\WpHelpers\Validation\Factory as ValidatorFactory;

trait ValidatesRequests
{
    /**
     * @param  array  $data
     * @param  array  $rules
     * @return mixed
     */
    public function validate(array $data, array $rules)
    {
        $factory = new ValidatorFactory;

        $validator = $factory->make($data, $rules);

        if ($validator->passes()) {
            return false;
        }

        return $validator->errors();
    }
}
