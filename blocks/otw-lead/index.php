<?php
/**
 * Expected: $attributes array available in scope.
 *
 * @var array<string,mixed> $attributes
 */

// Use a shared variable for the content to make the checks cleaner.
$content = $attributes['content'] ?? [];
$headline    = $content['headline']   ?? '';
$cta_text    = $content['ctaText']    ?? '';
$cta_url     = $content['ctaUrl']     ?? '';
$media       = $content['media']      ?? '';
$media_small = $content['mediaSmall'] ?? '';
?>
<div id="otw-lead-popover" class="otw-lead-container" role="dialog" aria-modal="true" aria-labelledby="otw-lead-headline" aria-describedby="otw-lead-desc">
    <button
        type="button"
        id="otw-lead-popover-close-btn"
        class="otw-lead-close-btn"
    >
        <span class="otw-lead-close-label screen-reader-text">Close</span>
        <span class="otw-lead-close-icon">&times;</span>
    </button>

    <span id="otw-lead-headline" class="otw-lead-headline screen-reader-text">
        <?php echo esc_html( $headline ); ?>
    </span>

    <a
        href="<?php echo esc_url( $cta_url ); ?>"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="<?php echo esc_attr( $cta_text ); ?>"
        class="otw-lead-cta-link"
    >
        <picture>
            <source media="(min-width: 600px)" srcset="<?php echo esc_url( $media ); ?>">
            <source media="(max-width: 599px)" srcset="<?php echo esc_url( $media_small ); ?>">
            <img
                src="<?php echo esc_url( $media_small ); ?>"
                alt="<?php echo esc_attr( $headline ); ?>"
            >
        </picture>
        <span id="otw-lead-desc" class="screen-reader-text"><?php echo esc_html( $cta_text ); ?></span>
    </a>
</div>

<div id="otw-lead-backdrop" class="otw-lead-backdrop"></div>
