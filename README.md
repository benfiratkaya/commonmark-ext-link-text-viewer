# Extension to change link content to url

[![PHP Composer](https://github.com/benfiratkaya/commonmark-ext-link-text-viewer/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/benfiratkaya/commonmark-ext-link-text-viewer/actions/workflows/php.yml)

This extension provides support for changing the content of links to url for [league/commonmark](https://github.com/thephpleague/commonmark) package version `^2.0`.

## Install

```bash
composer require benfiratkaya/commonmark-ext-link-text-viewer
```

## Example

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use BenFiratKaya\CommonMarkExtension\LinkTextViewerExtension;

$environment = new Environment([]);
$environment->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new LinkTextViewerExtension());

$converter = new MarkdownConverter($environment);
$html = $converter->convertToHtml('[text](http://example.test)');
```

This creates the following HTML

```html
<a href="http://example.test">http://example.test</a>
```

## Options

```php
//...
$environment = new Environment([]);
$environment->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new LinkTextViewerExtension());

$converter = new MarkdownConverter([
  'link_text_viewer' => [
      'internal_hosts' => '/(^|\.)internal\.test$/', // TODO: Don't forget to set this!
      'link_type' => 'all', // Set '' to disable. Variables: all, external, internal
  ],
], $environment)
$html = $converter->convertToHtml('[text](http://example.test)');
```

This creates the following HTML

```html
<a href="http://example.test">http://example.test</a>
```
