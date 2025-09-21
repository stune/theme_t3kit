<?php
namespace T3kit\themeT3kit\EventListener;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\Event\ModifyPageLayoutContentDrawItemEvent;

class ImageTextLinkPreviewRenderer
{
    public function __invoke(ModifyPageLayoutContentDrawItemEvent $event): void
    {
        $record = $event->getRecord();
        if (($record['CType'] ?? '') !== 'imageTextLink') {
            return;
        }

        $pageLayoutView = $event->getPageLayoutView();
        $itemContent = $event->getItemContent();

        if (!empty($record['bodytext'])) {
            $itemContent .= $pageLayoutView->linkEditContent(
                $pageLayoutView->renderText($record['bodytext']),
                $record
            ) . '<br />';
        }

        if (!empty($record['image'])) {
            $itemContent .= $pageLayoutView->linkEditContent(
                $pageLayoutView->getThumbCodeUnlinked($record, 'tt_content', 'image'),
                $record
            );

            $fileReferences = BackendUtility::resolveFileReferences('tt_content', 'image', $record);
            if (!empty($fileReferences)) {
                $linkedContent = '';
                foreach ($fileReferences as $fileReference) {
                    $description = $fileReference->getDescription();
                    if ($description !== null && $description !== '') {
                        $linkedContent .= htmlspecialchars($description) . '<br />';
                    }
                }

                if ($linkedContent !== '') {
                    $itemContent .= $pageLayoutView->linkEditContent($linkedContent, $record);
                }
            }
        }

        if (!empty($record['media'])) {
            $itemContent .= $pageLayoutView->linkEditContent(
                $pageLayoutView->getThumbCodeUnlinked($record, 'tt_content', 'media'),
                $record
            );
        }

        if (!empty($record['assets'])) {
            $itemContent .= $pageLayoutView->linkEditContent(
                $pageLayoutView->getThumbCodeUnlinked($record, 'tt_content', 'assets'),
                $record
            );
        }

        $event->setItemContent($itemContent);
        $event->setDrawItem(false);
    }
}
