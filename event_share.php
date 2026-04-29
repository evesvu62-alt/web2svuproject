<?php
declare(strict_types=1);

// generate share modal HTML for event pages
function renderShareModal(string $shareUrl, string $shareTitle): string
{
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
    
    ob_start();
    ?>
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="shareModalLabel">share this event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" 
                           class="btn btn-outline-primary" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            share on facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= $encodedTitle ?>&url=<?= $encodedUrl ?>" 
                           class="btn btn-outline-info" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            share on twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedUrl ?>" 
                           class="btn btn-outline-secondary" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            share on linkedin
                        </a>
                        <button type="button" 
                                class="btn btn-outline-success" 
                                onclick="copyToClipboard('<?= $encodedUrl ?>')">
                            copy link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>