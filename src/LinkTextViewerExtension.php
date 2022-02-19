<?php

declare(strict_types=1);

namespace BenFiratKaya\CommonMarkExtension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\ExternalLink\ExternalLinkProcessor;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

final class LinkTextViewerExtension implements ConfigurableExtensionInterface
{
  public function configureSchema(ConfigurationBuilderInterface $builder): void
  {
    $applyOptions = [
      ExternalLinkProcessor::APPLY_NONE,
      ExternalLinkProcessor::APPLY_ALL,
      ExternalLinkProcessor::APPLY_INTERNAL,
      ExternalLinkProcessor::APPLY_EXTERNAL,
    ];

    $builder->addSchema('link_text_viewer', Expect::structure([
      'internal_hosts' => Expect::type('string|string[]'),
      'link_type'      => Expect::anyOf(...$applyOptions)->default(ExternalLinkProcessor::APPLY_ALL),
    ]));
  }

  public function register(EnvironmentBuilderInterface $environment): void
  {
    $environment->addEventListener(DocumentParsedEvent::class, new LinkTextViewerProcessor($environment->getConfiguration()), -50);
  }
}
