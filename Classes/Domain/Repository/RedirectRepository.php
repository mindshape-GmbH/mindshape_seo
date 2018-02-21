<?php
/**
 * Created by PhpStorm.
 * User: alla
 * Date: 09.02.18
 * Time: 15:48
 */

namespace Mindshape\MindshapeSeo\Domain\Repository;


use Mindshape\MindshapeSeo\Service\SessionService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extensionmanager\Utility\DatabaseUtility;

class RedirectRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = array(
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    );

    /**
     * @return void
     */
    public function initializeObject()
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }


    /**
     * @param $sourceDomain
     * @param $sourcePath
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findBySourceDomainAndSourcePath($sourceDomain, $sourcePath, $currentUid)
    {

        $query = $this->createQuery();

        // Add query options
        $query->matching(
        // All conditions have to be met (AND)
            $query->logicalAND(
                $query->equals('sourceDomain', $sourceDomain),
                $query->equals('sourcePath', $sourcePath),
                $query->logicalNot(
                    $query->equals('uid', $currentUid)
                )
            )
        );
        return $query->execute();
    }


    /**
     * Finds redirect records by given filter parameter
     *
     * @param $searchedSourcePath
     * @param $searchedTarget
     * @param $searchedSourceDomain
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */

    public function findByFilter($searchedSourcePath, $searchedTarget, $searchedSourceDomain, $searchedHttpStatuscode)
    {
        // Create empty query = select * from table
        $query = $this->createQuery();

        // Add query options
        $query->matching(
        // One condition have to be met (OR)
            $query->logicalOR(
                $query->like('sourcePath', $searchedSourcePath),
                $query->like('target', $searchedTarget),
                $query->equals('sourceDomain', $searchedSourceDomain),
                $query->equals('httpStatuscode', $searchedHttpStatuscode)

            )
        );

        return $query->execute();
    }

    /**
     * Get all SYS Domains configured in TYPO3
     *
     * @return array
     */
    public function getSysDomains()
    {
        $sourceDomains = [];
        $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'uid, domainName',
            'sys_domain',
            '1 = 1',
            '',
            '',
            ''
        );


        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
            if ($row) {
                $sourceDomains[$row['uid']] = $row['domainName'];
            }
        }
        return $sourceDomains;
    }
}
