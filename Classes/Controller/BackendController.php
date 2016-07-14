<?php
namespace Mindshape\MindshapeSeo\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Dorndorf <dorndorf@mindshape.de>, mindshape GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Mindshape\MindshapeSeo\Domain\Model\Configuration;
use Mindshape\MindshapeSeo\Property\TypeConverter\UploadedFileReferenceConverter;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends ActionController
{
    /**
     * @var \Mindshape\MindshapeSeo\Domain\Repository\ConfigurationRepository
     * @inject
     */
    protected $configurationRepository;

    /**
     * @var \Mindshape\MindshapeSeo\Service\DomainService
     * @inject
     */
    protected $domainService;

    /**
     * @param string $domain
     * @return void
     */
    public function settingsAction($domain = Configuration::DEFAULT_DOMAIN)
    {
        $configuration = $this->configurationRepository->findByDomain($domain);

        if (null === $configuration) {
            $configuration = new Configuration();
            $configuration->setDomain($domain);
        }

        $this->view->assignMultiple(array(
            'domains' => $this->domainService->getAvailableDomains(),
            'currentDomain' => $domain,
            'configuration' => $configuration,
            'jsonldTypeOptions' => array(
                Configuration::JSONLD_TYPE_ORGANIZATION => LocalizationUtility::translate('tx_minshapeseo_configuration.jsonld.type.organization', 'mindshape_seo'),
                Configuration::JSONLD_TYPE_PERSON => LocalizationUtility::translate('tx_minshapeseo_configuration.jsonld.type.person', 'mindshape_seo'),
            ),
        ));
    }

    /**
     * @return void
     */
    public function initializeSaveConfigurationAction()
    {
        $this->setTypeConverterConfigurationForImageUpload('configuration');
    }

    /**
     * @param \Mindshape\MindshapeSeo\Domain\Model\Configuration $configuration
     * @return void
     */
    public function saveConfigurationAction(Configuration $configuration)
    {
        if ($configuration->_isNew()) {
            $this->configurationRepository->update($configuration);
        } else {
            $this->configurationRepository->add($configuration);
        }

        $this->redirect(
            'settings',
            'Backend',
            null,
            array(
                'domain' => $configuration->getDomain(),
            )
        );
    }

    /**
     * @return void
     */
    public function previewAction()
    {
    }

    /**
     * @param $argumentName
     * @return void
     */
    protected function setTypeConverterConfigurationForImageUpload($argumentName)
    {
        $uploadConfiguration = array(
            UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/mindshape_seo/',
        );

        /** @var PropertyMappingConfiguration $newExampleConfiguration */
        $newExampleConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
        $newExampleConfiguration
            ->forProperty('facebookDefaultImage')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                $uploadConfiguration
            )
            ->forProperty('jsonldLogo')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                $uploadConfiguration
            );
    }
}
