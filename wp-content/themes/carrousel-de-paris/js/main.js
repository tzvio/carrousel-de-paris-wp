jQuery(document).ready(function () {
    // Wait for lightbox to be available
    if (typeof lightbox !== 'undefined') {
        // Configure lightbox options without image counter
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': '',
            'disableScrolling': true,
            'fadeDuration': 300,
            'imageFadeDuration': 300,
            'maxWidth': 800,
            'maxHeight': 600,
            'positionFromTop': 100,
            'showImageNumberLabel': false
        });
    } else {
        console.log('Lightbox not loaded yet, waiting...');
        // Wait a bit and try again
        setTimeout(function () {
            if (typeof lightbox !== 'undefined') {
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true,
                    'albumLabel': '',
                    'disableScrolling': true,
                    'fadeDuration': 300,
                    'imageFadeDuration': 300,
                    'maxWidth': 800,
                    'maxHeight': 600,
                    'positionFromTop': 100,
                    'showImageNumberLabel': false
                });
            }
        }, 500);
    }

    // Initialize Glide.js Gallery
    if (typeof Glide !== 'undefined' && document.getElementById('carrousel-gallery')) {
        const glide = new Glide('#carrousel-gallery', {
            type: 'carousel',
            startAt: 0,
            perView: 3,
            focusAt: 'center',
            gap: 30,
            autoplay: 4000,
            hoverpause: true,
            keyboard: true,
            swipeThreshold: 80,
            dragThreshold: 120,
            breakpoints: {
                1024: {
                    perView: 2,
                    gap: 20
                },
                768: {
                    perView: 1,
                    gap: 15,
                    focusAt: 0
                },
                480: {
                    perView: 1,
                    gap: 10,
                    focusAt: 0
                }
            }
        });

        // Mount the gallery
        glide.mount();

        // Pause autoplay when lightbox is open
        jQuery(document).on('click', '[data-lightbox^="carrousel-gallery-"]', function () {
            glide.pause();
        });

        // Resume autoplay when lightbox is closed
        jQuery(document).on('lightbox:close', function () {
            glide.play();
        });
    }

    // Contact form validation and submission
    jQuery('#contactForm').on('submit', function (e) {
        e.preventDefault();
        // Remove previous error messages
        $('.field-error').remove();
        // Get form data
        const rawData = {
            firstName: $('#firstName').val().trim(),
            lastName: $('#lastName').val().trim(),
            email: $('#email').val().trim(),
            subject: $('#subject').val().trim(),
            message: $('#message').val().trim()
        };
        // Validate form
        if (!validateForm(rawData)) {
            return;
        }
        const formData = new FormData();
        for (const key in rawData) {
            formData.append(key, rawData[key]);
        }
        // Submit form
        submitForm(formData);
    });

    // Abstracted error rendering for form fields
    function renderFieldError(fieldId, message) {
        $(`#${fieldId}`).addClass('error');
        $(`#${fieldId}`).after(`<div class="field-error">${message}</div>`);
    }

    function validateForm(data) {
        // Reset any previous error states
        $('.contact-form input, .contact-form textarea').removeClass('error');
        $('.field-error').remove();

        let isValid = true;
        const fieldLabels = {
            firstName: 'Prénom',
            lastName: 'Nom',
            email: 'Adresse e-mail',
            subject: 'Sujet',
            message: 'Message'
        };

        // Check required fields
        Object.keys(data).forEach(key => {
            if (!data[key]) {
                renderFieldError(key, `${fieldLabels[key]} est requis.`);
                isValid = false;
            }
        });

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (data.email && !emailRegex.test(data.email)) {
            renderFieldError('email', 'Adresse e-mail invalide.');
            isValid = false;
        }

        if (!isValid) {
            showMessage('Veuillez remplir tous les champs obligatoires correctement.', 'error');
        }

        return isValid;
    }

    function submitForm(data) {
        const submitBtn = $('.submit-btn');
        const originalText = submitBtn.text();
        // Show loading state
        submitBtn.text('Envoi en cours...').prop('disabled', true);
        // Send data to Formspree
        fetch('https://formspree.io/f/xldnzzda', {
            method: 'POST',
            body: data,
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (response.ok) {
                    // Reset form
                    $('#contactForm')[0].reset();
                    // Show success message
                    showMessage('Votre message a été envoyé avec succès! Nous vous répondrons bientôt.', 'success');
                    // Optional: scroll to top of contact section
                    $('.contact-section')[0].scrollIntoView({ behavior: 'smooth' });
                } else {
                    return response.json().then(data => {
                        let msg = 'Une erreur est survenue.';
                        if (data.errors) {
                            msg = data.errors.map(e => e.message).join(', ');
                        }
                        showMessage(msg, 'error');
                    });
                }
            })
            .catch(() => {
                showMessage('Erreur de connexion. Veuillez réessayer.', 'error');
            })
            .finally(() => {
                // Always reset button state
                submitBtn.text(originalText).prop('disabled', false);
            });
    }

    function showMessage(text, type) {
        // Remove any existing messages
        $('.form-message').remove();

        // Create message element
        const messageClass = type === 'success' ? 'success' : 'error';
        const messageHtml = `
            <div class="form-message ${messageClass}">
                ${text}
            </div>
        `;

        // Insert message after form
        $('.contact-form').after(messageHtml);

        // Auto-remove message after 5 seconds
        setTimeout(() => {
            $('.form-message').fadeOut(300, function () {
                $(this).remove();
            });
        }, 5000);
    }
});
