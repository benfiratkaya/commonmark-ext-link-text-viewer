<?php

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;
use BenFiratKaya\CommonMarkExtension\LinkTextViewerExtension;

class LinkTextViewerExtensionTest extends TestCase
{
    protected function getConverter(array $config): MarkdownConverter
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension())->addExtension(new LinkTextViewerExtension());

        return new MarkdownConverter($environment);
    }

    public function testApplyForAllLinks(): void
    {
        $config = [
            'link_text_viewer' => [
                'link_type' => 'all',
            ],
        ];
        $converter = $this->getConverter($config);

        $linkMarkdown = '[test](http://example.test)';
        $html = $converter->convertToHtml($linkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://example.test">http://example.test</a>',
            $html
        );
    }

    public function testApplyForExternalLinks(): void
    {
        $config = [
            'link_text_viewer' => [
                'internal_hosts' => '/(^|\.)internal\.test$/',
                'link_type' => 'external',
            ],
        ];
        $converter = $this->getConverter($config);

        $internalLinkMarkdown = '[internal](http://internal.test)';
        $internalHtml = $converter->convertToHtml($internalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://internal.test">internal</a>',
            $internalHtml
        );

        $externalLinkMarkdown = '[external](http://external.test)';
        $externalHtml = $converter->convertToHtml($externalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://external.test">http://external.test</a>',
            $externalHtml
        );
    }

    public function testApplyForInternalLinks(): void
    {
        $config = [
            'link_text_viewer' => [
                'internal_hosts' => '/(^|\.)internal\.test$/',
                'link_type' => 'internal',
            ],
        ];
        $converter = $this->getConverter($config);

        $internalLinkMarkdown = '[internal](http://internal.test)';
        $internalHtml = $converter->convertToHtml($internalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://internal.test">http://internal.test</a>',
            $internalHtml
        );

        $externalLinkMarkdown = '[external](http://external.test)';
        $externalHtml = $converter->convertToHtml($externalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://external.test">external</a>',
            $externalHtml
        );
    }

    public function testDisableExt(): void
    {
        $config = [
            'link_text_viewer' => [
                'link_type' => '',
            ],
        ];
        $converter = $this->getConverter($config);

        $internalLinkMarkdown = '[internal](http://internal.test)';
        $internalHtml = $converter->convertToHtml($internalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://internal.test">internal</a>',
            $internalHtml
        );

        $externalLinkMarkdown = '[external](http://external.test)';
        $externalHtml = $converter->convertToHtml($externalLinkMarkdown);

        $this->assertStringContainsString(
            '<a href="http://external.test">external</a>',
            $externalHtml
        );
    }
}
