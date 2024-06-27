<?php

namespace App\Containerizer;

use App\Entity\HomeRow;

class HomeRowContainerizer extends LiveContainerizer implements ContainerizerInterface
{

    public function __construct(
        private readonly ContainerizerFactory $containerizer,
        private readonly HomeRow              $homeRow
    ) {}

    public function getContainers(): array {
        $containers = Array();
        $containerizer = $this->containerizer;

        /*
         * The getItems method calls all HomeRowItems related to the current homeRow instance
         */
        foreach ($this->homeRow->getItems() as $item) {
            /*
             * Calling the $containerizer object triggers the invoke function
             * which takes $item as an argument
             */
            $containerized = $containerizer($item);
            // Merge the retrieved containers into the empty $containers array
            $containers = array_merge($containers, $containerized->getContainers());
        }

        $this->items = $containers;
        $this->options = $this->homeRow->getOptions();
        $this->sort();
        $this->trim();

        return $this->items;
    }

    protected function sort(): array
    {
        if (array_key_exists('itemSortType', $this->options)) {
            $sort = $this->options['itemSortType'];
            if ($sort !== HomeRow::SORT_FIXED) {
                $this->items = parent::sort();
            }
        }
        return $this->items;
    }
}
