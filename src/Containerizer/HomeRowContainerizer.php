<?php

namespace App\Containerizer;

use App\Entity\HomeRow;

class HomeRowContainerizer extends LiveContainerizer implements ContainerizerInterface
{
    private ContainerizerFactory $containerizer;
    private HomeRow $homeRow;

    public function __construct(HomeRow $homeRow, ContainerizerFactory $containerizer) {
        $this->homeRow = $homeRow;
        $this->containerizer = $containerizer;
    }

    public function getContainers(): array {
        $containers = Array();
        $containerizer = $this->containerizer;
        foreach ($this->homeRow->getItems() as $item) {
            $containerized = $containerizer($item);
            $containers = array_merge($containers, $containerized->getContainers());
        }

        $this->items = $containers;
        $this->options = $this->homeRow->getOptions();
        $this->sort();
        $this->trim();

        return $this->items;
    }

    protected function sort(): Array
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
