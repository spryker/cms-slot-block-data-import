<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Zed\CmsSlotBlockDataImport\Business\DataImportStep;

use Spryker\Zed\CmsSlotBlockDataImport\Business\DataSet\CmsSlotBlockDataSetInterface;
use Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\ConditionResolverInterface;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class CmsPageKeysToIdsConditionsStep implements DataImportStepInterface
{
    /**
     * @var string
     */
    protected const KEY_CONDITION_CMS_PAGE = 'cms_page';

    /**
     * @uses \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\AllConditionResolver::KEY_ALL
     *
     * @var string
     */
    protected const KEY_ALL = 'all';

    /**
     * @uses \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\CmsPageKeysToIdsConditionResolver::KEY_CMS_PAGE_IDS
     *
     * @var string
     */
    protected const KEY_CMS_PAGE_IDS = 'cmsPageIds';

    /**
     * @var \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\ConditionResolverInterface
     */
    protected $allConditionsResolver;

    /**
     * @var \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\ConditionResolverInterface
     */
    protected $cmsPageKeysToIdsConditionsResolver;

    /**
     * @param \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\ConditionResolverInterface $allConditionsResolver
     * @param \Spryker\Zed\CmsSlotBlockDataImport\Business\Resolver\ConditionResolverInterface $categoryKeysToIdsConditionsResolver
     */
    public function __construct(
        ConditionResolverInterface $allConditionsResolver,
        ConditionResolverInterface $categoryKeysToIdsConditionsResolver
    ) {
        $this->allConditionsResolver = $allConditionsResolver;
        $this->cmsPageKeysToIdsConditionsResolver = $categoryKeysToIdsConditionsResolver;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $conditionsArray = $dataSet[CmsSlotBlockDataSetInterface::COL_CONDITIONS_ARRAY] ?? [];

        $conditionsArray[static::KEY_CONDITION_CMS_PAGE] = $this->allConditionsResolver->getConditions(
            $dataSet[CmsSlotBlockDataSetInterface::COL_CONDITIONS_CMS_PAGE_ALL],
        );

        $conditionsArray[static::KEY_CONDITION_CMS_PAGE] = $this->cmsPageKeysToIdsConditionsResolver->getConditions(
            $dataSet[CmsSlotBlockDataSetInterface::COL_CONDITIONS_CMS_PAGE_KEYS],
            $conditionsArray[static::KEY_CONDITION_CMS_PAGE],
        );

        if (!array_filter($conditionsArray[static::KEY_CONDITION_CMS_PAGE])) {
            return;
        }

        if ($conditionsArray[static::KEY_CONDITION_CMS_PAGE][static::KEY_CMS_PAGE_IDS]) {
            $conditionsArray[static::KEY_CONDITION_CMS_PAGE][static::KEY_ALL] = false;
        }

        $dataSet[CmsSlotBlockDataSetInterface::COL_CONDITIONS_ARRAY] = $conditionsArray;
    }
}
