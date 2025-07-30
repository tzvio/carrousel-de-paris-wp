# Carrousel de Paris WordPress Theme - Dynamic Content Guide

## Dynamic Content Management

This theme now supports full dynamic content management through the WordPress Customizer. All text content on the front page can be updated without editing code.

### How to Edit Content

1. **Log in to WordPress Admin**
2. **Go to Appearance > Customize**
3. **Edit content in the following sections:**

#### Hero Header Content
- Main Title (default: "SIMONE LUMBROSO")
- Subtitle (default: "DANS LE TOURBILLON DU")
- Featured Title/Glowing Text (default: "CARROUSEL DE PARIS")

#### Gallery Images
- Upload up to 6 custom gallery images
- Set alt text and titles for each image
- Images will fall back to default theme images if none are uploaded

#### About Section
- About Section Title (default: "L'HÉRITAGE DU CABARET")
- Four individual paragraphs of content
- Quote section
- Advanced: Custom HTML content (overrides individual paragraphs)

#### Contact Section
- Contact section title (default: "CONTACTEZ-NOUS")
- All form field placeholders:
  - First Name placeholder
  - Last Name placeholder
  - Email placeholder
  - Subject placeholder
  - Message placeholder
  - Submit button text
- Contact info display text:
  - Company name
  - Subtitle/description

#### Contact Information
- Phone number
- Email address

#### Form Messages
- Success message (when form is submitted successfully)
- Error message (when form submission fails)

#### Social Media Links
- Facebook URL
- Instagram URL
- Twitter URL
- YouTube URL

### Features

- **Live Preview**: All changes can be previewed in real-time using the WordPress Customizer
- **Fallback Content**: If no custom content is set, the theme uses sensible defaults
- **Secure**: All content is properly sanitized and escaped for security
- **SEO Friendly**: All text content is properly structured for search engines
- **Mobile Responsive**: All dynamic content adapts to different screen sizes

### Technical Notes

- All content is stored in WordPress theme modifications (`get_theme_mod()`)
- Content is cached for performance
- Gallery images support lightbox functionality
- Form submissions are handled via WordPress email functions
- Contact form includes nonce protection and validation

### Customization

To add more dynamic content options:

1. Add new customizer settings in `functions.php` using the `carrousel_customize_register` function
2. Update the corresponding template files to use `get_theme_mod()` with appropriate defaults
3. Use proper sanitization callbacks for security

### Support

For technical support or customization requests, please contact the theme developer.
