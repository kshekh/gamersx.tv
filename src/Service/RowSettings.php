<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Opis\JsonSchema\{ Validator, ValidationResult, ValidationError, Schema };


class RowSettings
{
    private $params;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    public function getSettings() {
        // Read in the JSON File and Schema
        $settingsFile = $this->params->get("app.row_settings");
        $schemaFile = $this->params->get("app.row_settings_schema");

        // Validate it
        $data = json_decode(file_get_contents($settingsFile));
        $schema = Schema::fromJsonString(file_get_contents($schemaFile));

        $validator = new Validator();

        $result = $validator->schemaValidation($data, $schema);
        // Return as a PHP Array
        if ($result->isValid()) {
            return $data;
        } else {
            // /** @var ValidationError $error */
            // $error = $result->getFirstError();
            // echo '$data is invalid', PHP_EOL;
            // echo "Error: ", $error->keyword(), PHP_EOL;
            // echo json_encode($error->keywordArgs(), JSON_PRETTY_PRINT), PHP_EOL;
            // dd($error);
            return $result;
        }
    }

}
