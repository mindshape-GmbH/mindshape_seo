<?php
namespace Mindshape\MindshapeSeo\Property\TypeConverter;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Dorndorf <dorndorf@mindshape.de>
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

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as FalFileReference;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package mindshape_seo
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class UploadedFileReferenceConverter extends AbstractTypeConverter
{

    /**
     * Folder where the file upload should go to (including storage).
     */
    const CONFIGURATION_UPLOAD_FOLDER = 1;

    /**
     * How to handle a upload when the name of the uploaded file conflicts.
     */
    const CONFIGURATION_UPLOAD_CONFLICT_MODE = 2;

    /**
     * Whether to replace an already present resource.
     * Useful for "maxitems = 1" fields and properties
     * with no ObjectStorage annotation.
     */
    const CONFIGURATION_ALLOWED_FILE_EXTENSIONS = 4;

    /**
     * @var string
     */
    protected $defaultUploadFolder = '1:/user_upload/';

    /**
     * @var string
     */
    protected $defaultConflictMode = DuplicationBehavior::RENAME;

    /**
     * @var array<string>
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = FileReference::class;

    /**
     * Take precedence over the available FileReferenceConverter
     *
     * @var int
     */
    protected $priority = 2;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     */
    protected $resourceFactory;

    /**
     * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
     */
    protected $hashService;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var \TYPO3\CMS\Core\Resource\FileInterface[]
     */
    protected $convertedResources = [];

    /**
     * @param \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory
     * @param \TYPO3\CMS\Extbase\Security\Cryptography\HashService $hashService
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function __construct(
        ResourceFactory $resourceFactory,
        HashService $hashService,
        PersistenceManager $persistenceManager
    ) {
        $this->resourceFactory = $resourceFactory;
        $this->hashService = $hashService;
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param array $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface|null $configuration
     * @return \TYPO3\CMS\Core\Resource\FileInterface|\TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder|\TYPO3\CMS\Extbase\Error\Error
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     * @throws \TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        if (
            !array_key_exists('error', $source) ||
            $source['error'] === \UPLOAD_ERR_NO_FILE
        ) {
            if (
                array_key_exists('submittedFile', $source) &&
                array_key_exists('resourcePointer', $source['submittedFile'])
            ) {
                try {
                    $resourcePointer = $this->hashService->validateAndStripHmac($source['submittedFile']['resourcePointer']);

                    if (strpos($resourcePointer, 'file:') === 0) {
                        $fileUid = substr($resourcePointer, 5);

                        return $this->createFileReferenceFromFalFileObject(
                            $this->resourceFactory->getFileObject($fileUid)
                        );
                    } else {
                        return $this->createFileReferenceFromFalFileReferenceObject(
                            $this->resourceFactory->getFileReferenceObject($resourcePointer),
                            $resourcePointer
                        );
                    }

                } catch (\InvalidArgumentException $e) {
                    // Nothing to do. No file is uploaded and resource pointer is invalid. Discard!
                }
            }

            return null;
        }

        if ($source['error'] !== \UPLOAD_ERR_OK) {
            switch ($source['error']) {
                case \UPLOAD_ERR_INI_SIZE:
                case \UPLOAD_ERR_FORM_SIZE:
                case \UPLOAD_ERR_PARTIAL:
                    return new Error($this->getUploadErrorMessage($source['error']), 1264440823);
                default:
                    return new Error('An error occurred while uploading. Please try again or contact the administrator if the problem remains', 1340193849);
            }
        }

        if (array_key_exists($source['tmp_name'], $this->convertedResources)) {
            return $this->convertedResources[$source['tmp_name']];
        }

        try {
            $resource = $this->importUploadedResource($source, $configuration);
        } catch (\Exception $e) {
            return new Error($e->getMessage(), $e->getCode());
        }

        $this->convertedResources[$source['tmp_name']] = $resource;

        return $resource;
    }

    /**
     * Import a resource and respect configuration given for properties
     *
     * @param array $uploadInfo
     * @param PropertyMappingConfigurationInterface $configuration
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TypeConverterException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException
     * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException
     */
    protected function importUploadedResource(array $uploadInfo, PropertyMappingConfigurationInterface $configuration)
    {
        if (!GeneralUtility::makeInstance(FileNameValidator::class)->isValid($uploadInfo['name'])) {
            throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1399312430);
        }

        $uploadInfo['name'] = $this->sanitizeFilename($uploadInfo['name']);

        $allowedFileExtensions = $configuration->getConfigurationValue(UploadedFileReferenceConverter::class, self::CONFIGURATION_ALLOWED_FILE_EXTENSIONS);

        if ($allowedFileExtensions !== null) {
            $filePathInfo = PathUtility::pathinfo($uploadInfo['name']);

            if (!GeneralUtility::inList($allowedFileExtensions, strtolower($filePathInfo['extension']))) {
                throw new TypeConverterException('File extension is not allowed!', 1399312430);
            }
        }

        $uploadFolderId = $configuration->getConfigurationValue(UploadedFileReferenceConverter::class, self::CONFIGURATION_UPLOAD_FOLDER) ?: $this->defaultUploadFolder;
        $conflictMode = $configuration->getConfigurationValue(UploadedFileReferenceConverter::class, self::CONFIGURATION_UPLOAD_CONFLICT_MODE) ?: $this->defaultConflictMode;

        $uploadFolder = $this->resourceFactory->retrieveFileOrFolderObject($uploadFolderId);
        $uploadedFile = $uploadFolder->addUploadedFile($uploadInfo, $conflictMode);

        $resourcePointer = array_key_exists('submittedFile', $uploadInfo) &&
        array_key_exists('resourcePointer', $uploadInfo['submittedFile']) &&
        strpos($uploadInfo['submittedFile']['resourcePointer'], 'file:') === false
            ? $this->hashService->validateAndStripHmac($uploadInfo['submittedFile']['resourcePointer'])
            : null;

        $fileReferenceModel = $this->createFileReferenceFromFalFileObject($uploadedFile, $resourcePointer);

        return $fileReferenceModel;
    }

    /**
     * @param $filename
     * @return string
     */
    protected function sanitizeFilename($filename)
    {
        return strtolower(preg_replace('/[^0-9a-zA-Z\.]+/', '', trim($filename)));
    }

    /**
     * @param File $file
     * @param int $resourcePointer
     * @return \Mindshape\MindshapeSeo\Domain\Model\FileReference
     */
    protected function createFileReferenceFromFalFileObject(File $file, $resourcePointer = null)
    {
        $fileReference = $this->resourceFactory->createFileReferenceObject([
            'uid_local' => $file->getUid(),
            'uid_foreign' => uniqid('NEW_', false),
            'uid' => uniqid('NEW_', false),
            'crop' => null,
        ]);

        return $this->createFileReferenceFromFalFileReferenceObject($fileReference, $resourcePointer);
    }

    /**
     * @param FalFileReference $falFileReference
     * @param int $resourcePointer
     * @return \Mindshape\MindshapeSeo\Domain\Model\FileReference
     */
    protected function createFileReferenceFromFalFileReferenceObject(FalFileReference $falFileReference, $resourcePointer = null)
    {
        if ($resourcePointer === null) {
            /** @var \Mindshape\MindshapeSeo\Domain\Model\FileReference $fileReference */
            $fileReference = $this->objectManager->get(FileReference::class);
        } else {
            $fileReference = $this->persistenceManager->getObjectByIdentifier(
                $resourcePointer,
                FileReference::class,
                false
            );
        }

        $fileReference->setOriginalResource($falFileReference);

        return $fileReference;
    }

    /**
     * @param int $errorCode
     * @return string
     */
    protected function getUploadErrorMessage(int $errorCode): string
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);

        switch ($errorCode) {
            case \UPLOAD_ERR_INI_SIZE:
                $logger->error('The uploaded file exceeds the upload_max_filesize directive in php.ini.', []);

                return LocalizationUtility::translate('upload.error.150530345', 'form');
            case \UPLOAD_ERR_FORM_SIZE:
                $logger->error('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', []);

                return LocalizationUtility::translate('upload.error.150530345', 'form');
            case \UPLOAD_ERR_PARTIAL:
                $logger->error('The uploaded file was only partially uploaded.', []);

                return LocalizationUtility::translate('upload.error.150530346', 'form');
            case \UPLOAD_ERR_NO_FILE:
                $logger->error('No file was uploaded.', []);

                return LocalizationUtility::translate('upload.error.150530347', 'form');
            case \UPLOAD_ERR_NO_TMP_DIR:
                $logger->error('Missing a temporary folder.', []);

                return LocalizationUtility::translate('upload.error.150530348', 'form');
            case \UPLOAD_ERR_CANT_WRITE:
                $logger->error('Failed to write file to disk.', []);

                return LocalizationUtility::translate('upload.error.150530348', 'form');
            case \UPLOAD_ERR_EXTENSION:
                $logger->error('File upload stopped by extension.', []);

                return LocalizationUtility::translate('upload.error.150530348', 'form');
            default:
                $logger->error('Unknown upload error.', []);

                return LocalizationUtility::translate('upload.error.150530348', 'form');
        }
    }
}
