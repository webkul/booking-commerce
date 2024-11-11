/*** Use For admin side. */
"use strict";
document.addEventListener('DOMContentLoaded', function () {
    jQuery('#wkbooking_page_select' ).select2();
 });

 /**
 * Copy functionality for short code.
 */
function wkbooking_copy_shortcode() {
    const shortcodeField = document.getElementById('wkbooking_shortcode_field');

    // Check if the Clipboard API is supported.
    if (navigator.clipboard && navigator.clipboard.writeText) {
        // Use the Clipboard API.
        navigator.clipboard.writeText(shortcodeField.value)
            .then(() => {
                wkbcShowCopyMessage();
            })
            .catch(err => {
                console.error('Could not copy text: ', err);
            });
    } else {
        // Fallback for older browsers
        const tempTextArea = document.createElement('textarea');
        tempTextArea.value = shortcodeField.value;
        document.body.appendChild(tempTextArea);
        tempTextArea.select();
        try {
            document.execCommand('copy');
            wkbcShowCopyMessage();
        } catch (err) {
            console.error('Fallback: Could not copy text: ', err);
        } finally {
            document.body.removeChild(tempTextArea);
        }
    }
}

// Function to display the copy message.
function wkbcShowCopyMessage() {
    const messageElement = document.getElementById('wkbooking_copy_message');
    messageElement.style.display = 'block';
    messageElement.textContent = wkbooking_params.copyMessage; // Use the localized string.

    // Hide the message after a few seconds.
    setTimeout(() => {
        messageElement.style.display = 'none';
    }, 2000);
}

function wkbooking_validate_domain() {
    var domainInput = document.querySelector('input[name="wkbooking_link"]');
    var submitButton = document.querySelector('input[type="submit"]');
    var errorMessage = document.getElementById('wkbooking_domain_error');
    var domainPattern = /^https?:\/\/[a-zA-Z0-9-]+\.bookingcommerce\.com$/;

    if ( ! domainPattern.test( domainInput.value ) ) {
        if ( ! errorMessage ) {
            errorMessage = document.createElement('div');
            errorMessage.id = 'wkbooking_domain_error';
            errorMessage.className = 'wkbooking-error-message';
            errorMessage.textContent = 'Please enter a valid domain in the format http://subdomain.bookingcommerce.com';
            domainInput.parentNode.appendChild(errorMessage);
        } else {
            errorMessage.style.display = 'block';
        }
        submitButton.disabled = true;
    } else {
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
        submitButton.disabled = false;
    }
}
// Check if the input element exists before attaching the event listener.
var bookingLinkInput = document.querySelector('input[name="wkbooking_link"]');

if ( bookingLinkInput ) {
    // If the input element exists, add the event listener.
    bookingLinkInput.addEventListener('input', wkbooking_validate_domain);
}
