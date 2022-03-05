<?php

declare(strict_types=1);

namespace BenFiratKaya\CommonMarkExtension;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\Config\ConfigurationInterface;
use League\CommonMark\Node\Inline\Text;

final class LinkTextViewerProcessor
{
  public const APPLY_NONE = '';
  public const APPLY_ALL = 'all'; // tüm linkler
  public const APPLY_EXTERNAL = 'external'; // sadece dış linkler
  public const APPLY_INTERNAL = 'internal'; // sadece iç linkler

  /** @psalm-readonly */
  private ConfigurationInterface $config;

  public function __construct(ConfigurationInterface $config)
  {
    $this->config = $config;
  }

  public function __invoke(DocumentParsedEvent $e): void
  {
    $internalHosts = $this->config->get('link_text_viewer/internal_hosts');
    $link_type = $this->config->get('link_text_viewer/link_type');
    
    if ($link_type === self::APPLY_NONE)
      return;

    foreach ($e->getDocument()->iterator() as $link) {
      if (!($link instanceof Link))
        continue;

      // Something is terribly wrong with this URL
      $host = parse_url($link->getUrl(), PHP_URL_HOST);
      if (!is_string($host))
        continue;

      $isInternalHost = self::hostMatches($host, $internalHosts);
      if (
        $link_type !== self::APPLY_ALL &&
        ($isInternalHost && $link_type === self::APPLY_EXTERNAL) || (!$isInternalHost && $link_type === self::APPLY_INTERNAL)
      )
        continue;

      $this->setLinkText($link);
    }
  }

  private function setLinkText(Link $link): void
  {
    $link->replaceChildren([new Text($link->getUrl())]);
  }

  /**
   * @param string $host
   * @param mixed $compareTo
   * @return bool
   * @internal This method is only public so we can easily test it. DO NOT USE THIS OUTSIDE OF THIS EXTENSION!
   */
  public static function hostMatches(string $host, mixed $compareTo): bool
  {
    foreach ((array)$compareTo as $c) {
      if (str_starts_with($c, '/')) {
        if (\preg_match($c, $host)) {
          return true;
        }
      } elseif ($c === $host) {
        return true;
      }
    }

    return false;
  }
}
