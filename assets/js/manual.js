/**
 * ClearCut — manual.js
 *
 * Enhances the User Manual page with smooth scrolling
 * for the table of contents links.
 */

(function () {
    'use strict';

    /**
     * Intercept clicks on table-of-contents anchors and scroll smoothly
     * to the target section, accounting for the sticky header height.
     */
    document.querySelectorAll('.toc-list a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            const targetId = link.getAttribute('href');
            const target   = document.querySelector(targetId);

            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });

                // Update the URL hash without a page jump
                if (history.pushState) {
                    history.pushState(null, '', targetId);
                }
            }
        });
    });

}());
