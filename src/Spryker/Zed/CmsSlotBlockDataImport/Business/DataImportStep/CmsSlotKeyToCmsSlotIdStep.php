<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsSlotBlockDataImport\Business\DataImportStep;

use Orm\Zed\CmsSlot\Persistence\Map\SpyCmsSlotTableMap;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotQuery;
use Spryker\Zed\CmsSlotBlockDataImport\Business\DataSet\CmsSlotBlockDataSetInterface;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class CmsSlotKeyToCmsSlotIdStep implements DataImportStepInterface
{
    /**
     * @var int[]
     */
    protected $idCmsSlotCache = [];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $dataSet[CmsSlotBlockDataSetInterface::CMS_SLOT_ID] = $this->getIdCmsSlotByKey(
            $dataSet[CmsSlotBlockDataSetInterface::CMS_SLOT_KEY]
        );
    }

    /**
     * @param string $cmsSlotKey
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException
     *
     * @return int
     */
    protected function getIdCmsSlotByKey(string $cmsSlotKey): int
    {
        if (isset($this->idCmsSlotCache[$cmsSlotKey])) {
            return $this->idCmsSlotCache[$cmsSlotKey];
        }

        $idCmsSlot = SpyCmsSlotQuery::create()
            ->filterByKey($cmsSlotKey)
            ->select([SpyCmsSlotTableMap::COL_ID_CMS_SLOT])
            ->findOne();

        if (!$idCmsSlot) {
            throw new EntityNotFoundException(sprintf('Could not find CMS slot ID by key "%s".', $cmsSlotKey));
        }

        $this->idCmsSlotCache[$cmsSlotKey] = $idCmsSlot;

        return $idCmsSlot;
    }
}