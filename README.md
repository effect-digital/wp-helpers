<p align="center">
<a href="https://travis-ci.org/industrious/wp-helpers"><img src="https://travis-ci.org/industrious/wp-helpers.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/industrious/wp-helpers"><img src="https://poser.pugx.org/industrious/wp-helpers/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/industrious/wp-helpers"><img src="https://poser.pugx.org/industrious/wp-helpers/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/industrious/wp-helpers"><img src="https://poser.pugx.org/industrious/wp-helpers/license.svg" alt="License"></a>
</p>

## Introduction

industrious/wp-helpers is a [[]].

## License

industrious/wp-helpers is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

To get started with industrious/wp-helpers, use Composer to add the package to your project's dependencies:

    composer require industrious-mouse/wp-helpers

# Examples

## Validation Helpers

Below is an example of how you might be able to use the ValidatesRequests trait, to validate a form.

    use ValidatesRequests;

    /**
     *
     */
    public function __construct()
    {
        $data = [
            'abc' => '123',
            'def' => '123',
        ];

        $rules = [
            'abc' => 'required',
            'def' => 'required',
        ];

        $errors = $this->validate($data, $rules);

        if (! $errors) {
            wp_send_json_success();
        }

        wp_send_json_error([
            'errors' => $errors->toArray()
        ]);
    }
