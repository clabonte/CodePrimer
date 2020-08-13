<?php

namespace CodePrimer\Helper;

use CodePrimer\Model\Derived\Event;

class EventHelper
{
    public function getNamedData(Event $event): array
    {
        $this->setDataNames($event);

        $data = [];
        foreach ($event->getDataBundles() as $dataBundle) {
            foreach ($dataBundle->getData() as $list) {
                foreach ($list as $item) {
                    $data[$item->getName()] = $item;
                }
            }
        }

        return $data;
    }

    /**
     * Sets a unique name attribute for all the data elements that are part of an Event. This method should be called prior
     * to generate any event artifact to ensure each data has a unique name in the corresponding class.
     *
     * @param Event $event The event to set the data names for
     */
    public function setDataNames(Event $event)
    {
        $nameHelper = new DataNameHelper();

        foreach ($event->getDataBundles() as $bundleName => $dataBundle) {
            foreach ($dataBundle->getData() as $modelName => $list) {
                foreach ($list as $data) {
                    $conflicts = $nameHelper->assignDataName($bundleName, $data);
                    // Handle naming conflicts by renaming associated data
                    while (!empty($conflicts)) {
                        $newConflicts = [];
                        foreach ($conflicts as $conflict) {
                            $otherData = $nameHelper->getData($conflict);
                            $otherBundleName = $nameHelper->getBundleName($conflict);
                            if ($otherData->getName() === $conflict) {
                                // Data still use the conflicting name, let's rename it
                                // This exercise may generate new conflicts that must also be handled...
                                $newConflicts = array_merge($newConflicts, $nameHelper->assignDataName($otherBundleName, $otherData));
                            }
                        }
                        $conflicts = $newConflicts;
                    }
                }
            }
        }
    }
}
