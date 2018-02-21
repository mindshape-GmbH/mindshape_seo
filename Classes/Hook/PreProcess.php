<?php
/**
 * Created by PhpStorm.
 * User: alla
 * Date: 19.02.18
 * Time: 17:29
 */

namespace Mindshape\MindshapeSeo\Hook;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class PreProcess
{
    /**
     * @param array $parameters
     * @param array $parent
     */
    public function redirect(array $parameters, array $parent)
    {
        $requestUri = GeneralUtility::getIndpEnv('REQUEST_URI');
        $target = $this->findRedirect($requestUri);
        if (is_null($target) || !$target) {
            return;
        }
        $httpStatus = \constant(HttpUtility::class . '::HTTP_STATUS_' . (int)$target['http_statuscode']);
        if (empty($httpStatus)) {
            // constant is not defined
            return;
        }

        $table = 'tx_mindshapeseo_domain_model_redirect';
        $where = '1=1 AND uid='. $target['uid'];
        $result = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, array('hits'=>(int)($target['hits'] + 1), 'last_hit_on'=> time()));

        DebuggerUtility::var_dump($result);
        exit;
        HttpUtility::redirect($target['target'], $httpStatus);

        /** @var TYPE_NAME $result */
        return $result;

    }

    /**
     * @param $requestUri
     * @return mixed
     */
    protected function findRedirect($requestUri)
    {
        $target = $this->findDirectRedirect($requestUri);
        if (is_null($target) || !$target) {
            $target = $this->findRegExpRedirect($requestUri);
        }
        return $target;
    }

    protected function findDirectRedirect($requestUri)
    {
        $sysDomainUid = $this->getSysDomainUid();
        $where = '';
        $where .= '1=1 AND hidden=0 AND deleted=0 AND regex=0 AND source_path=\'' . htmlspecialchars($requestUri) . '\'';
        if ($sysDomainUid) {
            $where .= ' AND (source_domain=0 OR source_domain=' . (int)$sysDomainUid . ')';
        }
        DebuggerUtility::var_dump($where);


        $result = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            '*',
            'tx_mindshapeseo_domain_model_redirect',
            $where,
            '',
            '',
            ''
        );

        return $result;
    }

    /**
     * Find redirect by RegExp
     *
     * @param string $requestUri
     *
     * @return array
     */
    protected function findRegExpRedirect($requestUri)
    {
        $where   = '1=1 AND hidden=0 AND deleted=0 AND regex=1';
        $results = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            '*',
            'tx_urlredirect_domain_model_redirect',
            $where,
            '',
            '',
            ''
        );

        if (is_null($results))  {
            return null;
        }

        $redirect = [];
        foreach ($results as $redirect) {
            if (preg_match('@' . $redirect['source_path'] . '@', $requestUri)) {
                $redirect['target'] = preg_replace(
                    '@' . $redirect['source_path'] . '@',
                    $redirect['target'],
                    $requestUri
                );
                break;
            } else {
                $redirect = null;
            }
        }
        return $redirect;
    }

    protected function getSysDomainUid()
    {
        $domain = 0;

        $table = 'sys_domain';
        $fields = 'uid';
        $where = sprintf(
            'domainName=\'%s\' AND redirectTo=\'\' AND hidden=0',
            htmlspecialchars(GeneralUtility::getIndpEnv('HTTP_HOST'))
        );


        $result = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                $fields,
                $table,
                $where,
                '',
                '',
                ''
        );

        if (isset($result['uid'])) {
            $domain = (int)$result['uid'];
        }


        return $domain;
    }
}