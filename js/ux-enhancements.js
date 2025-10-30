/**
 * UX Enhancements using jQuery (already imported)
 * Minimal enhancements leveraging Bootstrap's existing functionality
 */

$(document).ready(function() {
    'use strict';

    // Add hover effect to cards with stretched links (Bootstrap doesn't animate transform by default)
    $('.card:has(a.stretched-link)').hover(
        function() {
            $(this).addClass('shadow');
        },
        function() {
            $(this).removeClass('shadow').addClass('shadow-sm');
        }
    );

    // Add visual feedback for form submissions
    $('form').on('submit', function() {
        var $submitBtn = $(this).find('button[type="submit"]');
        if ($submitBtn.length) {
            $submitBtn.prop('disabled', true);
            var originalText = $submitBtn.html();
            $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + originalText);
        }
    });

    // Smooth scroll for anchor links (if any)
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 300);
        }
    });

    // Add visual feedback for checkbox form submissions
    $('input[type="checkbox"][onchange*="submit"]').on('change', function() {
        var $parent = $(this).closest('td');
        if ($parent.length) {
            $parent.fadeTo(200, 0.5, function() {
                $(this).fadeTo(200, 1.0);
            });
        }
    });
});
