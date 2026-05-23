<?php

declare(strict_types=1);

namespace Mindshape\MindshapeSeo\Event;

/***
 *
 * This file is part of the "mindshape SEO" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 ***/

use Mindshape\MindshapeSeo\Domain\Model\Configuration;

/**
 * PSR-14 event dispatched right before the JSON-LD payload is rendered into the
 * page header. Listeners may freely manipulate the JSON-LD array; the modified
 * array is what is serialised and added via PageRenderer::addHeaderData().
 *
 * Replaces the legacy userFunc hook
 * `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonld_preRendering']`,
 * which is still invoked for backwards compatibility but triggers an
 * E_USER_DEPRECATED notice. Migrate listeners to this event.
 */
final class BeforeJsonLdRenderingEvent
{
    /**
     * @param array<string, mixed> $jsonLd
     */
    public function __construct(
        private array $jsonLd,
        private readonly ?Configuration $domainConfiguration
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getJsonLd(): array
    {
        return $this->jsonLd;
    }

    /**
     * @param array<string, mixed> $jsonLd
     */
    public function setJsonLd(array $jsonLd): void
    {
        $this->jsonLd = $jsonLd;
    }

    public function getDomainConfiguration(): ?Configuration
    {
        return $this->domainConfiguration;
    }
}
