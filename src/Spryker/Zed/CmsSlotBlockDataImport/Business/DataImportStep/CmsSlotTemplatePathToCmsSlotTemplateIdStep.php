<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\CmsSlotBlockDataImport\Business\DataImportStep;

use Orm\Zed\CmsSlot\Persistence\Map\SpyCmsSlotTemplateTableMap;
use Orm\Zed\CmsSlot\Persistence\SpyCmsSlotTemplateQuery;
use Spryker\Zed\CmsSlotBlockDataImport\Business\DataSet\CmsSlotBlockDataSetInterface;
use Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException;
use Spryker\Zed\DataImport\Business\Exception\InvalidDataException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class CmsSlotTemplatePathToCmsSlotTemplateIdStep implements DataImportStepInterface
{
    /**
     * @var array<int>
     */
    protected $idCmsSlotTemplateBuffer = [];

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        if (!$dataSet[CmsSlotBlockDataSetInterface::COL_SLOT_TEMPLATE_PATH]) {
            throw new InvalidDataException(sprintf('Column %s is required, please check the data.', CmsSlotBlockDataSetInterface::COL_SLOT_TEMPLATE_PATH));
        }

        $dataSet[CmsSlotBlockDataSetInterface::COL_SLOT_TEMPLATE_ID] = $this->getTemplateIdByPath(
            $dataSet[CmsSlotBlockDataSetInterface::COL_SLOT_TEMPLATE_PATH],
        );
    }

    /**
     * @param string $templatePath
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\EntityNotFoundException
     *
     * @return int
     */
    protected function getTemplateIdByPath(string $templatePath): int
    {
        if (isset($this->idCmsSlotTemplateBuffer[$templatePath])) {
            return $this->idCmsSlotTemplateBuffer[$templatePath];
        }

        /** @var int|null $idCmsSlotTemplate */
        $idCmsSlotTemplate = SpyCmsSlotTemplateQuery::create()
            ->filterByPath($templatePath)
            ->select([SpyCmsSlotTemplateTableMap::COL_ID_CMS_SLOT_TEMPLATE])
            ->findOne();

        if (!$idCmsSlotTemplate) {
            throw new EntityNotFoundException(sprintf('Could not find CMS slot template ID by path "%s".', $templatePath));
        }

        $this->idCmsSlotTemplateBuffer[$templatePath] = $idCmsSlotTemplate;

        return $idCmsSlotTemplate;
    }
}
