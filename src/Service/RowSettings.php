<?php

namespace App\Service;

use App\Entity\HomeRow;
use App\Entity\HomeRowItem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Opis\JsonSchema\{ Validator, ValidationResult, ValidationError, Schema };
use Symfony\Component\Filesystem\Filesystem;


class RowSettings
{
    private $params;
    private $files;

    public function __construct(Filesystem $files, ParameterBagInterface $params) {
        $this->params = $params;
        $this->files = $files;
    }

    public function toEntities($settings) {
        $rows = [];

        foreach ($settings->rows as $settingsRow) {
            $homeRow = new HomeRow();

            $homeRow->setTitle($settingsRow->title);
            $homeRow->setSort($settingsRow->sort);
            $homeRow->setItemType($settingsRow->itemType);

            foreach ($settingsRow->items as $index => $settingsItem) {
                $homeRowItem = new HomeRowItem();
                $homeRowItem->setTwitchId($settingsItem->twitchId);
                $homeRowItem->setShowArt($settingsItem->showArt);
                $homeRowItem->setOfflineDisplayType($settingsItem->offlineDisplayType);
                $homeRowItem->setLinkType($settingsItem->linkType);
                if ($homeRow->getSort() === HomeRow::SORT_FIXED) {
                    $homeRowItem->setSortIndex($index);
                }

                $homeRowItem->setHomeRow($homeRow);
                $homeRow->addItem($homeRowItem);
            }

            $rows[] = $homeRow;
        }

        return $rows;
    }

    public function getSettingsFromJsonFile($settingsFile) {
        // Read in the JSON File and Schema
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
            $error = $result->getFirstError();
            $message = "JSON Error: ". $error->keyword(). PHP_EOL.
                json_encode($error->keywordArgs());
            throw new \Exception($message);
        }
    }

    public function writeSettingsToJsonFile($rows) {
        $settingsFile = $this->params->get("app.row_settings");
        $contents = json_encode($rows, JSON_PRETTY_PRINT);
        $this->files->dumpFile($settingsFile, $contents);
    }

}
