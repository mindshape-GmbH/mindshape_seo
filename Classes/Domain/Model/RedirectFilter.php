<?php
/**
 * Created by PhpStorm.
 * User: alla
 * Date: 13.02.18
 * Time: 16:36
 */

namespace Mindshape\MindshapeSeo\Domain\Model;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class RedirectFilter extends AbstractEntity
{
    /**
     * @var
     */

    protected $searchSourcePath;

    /**
     * @var
     */
    protected $searchTarget;

    /**
     * @var
     */
    protected $searchSourceDomain;

    /**
     * @var
     */
    protected $searchHttpStatusCode;

    /**
     * @return mixed
     */
    public function getSearchSourcePath()
    {
        return $this->searchSourcePath;
    }

    /**
     * @param mixed $searchSourcePath
     */
    public function setSearchSourcePath($searchSourcePath)
    {
        $this->searchSourcePath = $searchSourcePath;
    }

    /**
     * @return mixed
     */
    public function getSearchTarget()
    {
        return $this->searchTarget;
    }

    /**
     * @param mixed $searchTarget
     */
    public function setSearchTarget($searchTarget)
    {
        $this->searchTarget = $searchTarget;
    }

    /**
     * @return mixed
     */
    public function getSearchHttpStatusCode()
    {
        return $this->searchHttpStatusCode;
    }

    /**
     * @param mixed $searchHttpStatusCode
     */
    public function setSearchHttpStatusCode($searchHttpStatusCode)
    {
        $this->searchHttpStatusCode = $searchHttpStatusCode;
    }

    /**
     * @return mixed
     */
    public function getSearchSourceDomain()
    {
        return $this->searchSourceDomain;
    }

    /**
     * @param mixed $searchSourceDomain
     */
    public function setSearchSourceDomain($searchSourceDomain)
    {
        $this->searchSourceDomain = $searchSourceDomain;
    }


}