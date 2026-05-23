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
 * PSR-14 event dispatched right before the JSON-LD breadcrumb payload is
 * rendered into the page footer. Listeners may freely manipulate the breadcrumb
 * structure; the modified array is what is serialised and added via
 * PageRenderer::addFooterData().
 *
 * Replaces the legacy userFunc hook
 * `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mindshape_seo']['jsonldBreadcrumb_preRendering']`,
 * which is still invoked for backwards compatibility but triggers an
 * E_USER_DEPRECATED notice. Migrate listeners to this event.
 */
final class BeforeJsonLdBreadcrumbRenderingEvent
{
    /**
     * @param array<string, mixed> $jsonLdBreadcrumb
     */
    public function __construct(
        private array $jsonLdBreadcrumb,
        private readonly ?Configuration $domainConfiguration
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getJsonLdBreadcrumb(): array
    {
        return $this->jsonLdBreadcrumb;
    }

    /**
     * @param array<string, mixed> $jsonLdBreadcrumb
     */
    public function setJsonLdBreadcrumb(array $jsonLdBreadcrumb): void
    {
        $this->jsonLdBreadcrumb = $jsonLdBreadcrumb;
    }

    public function getDomainConfiguration(): ?Configuration
    {
        return $this->domainConfiguration;
    }
}
