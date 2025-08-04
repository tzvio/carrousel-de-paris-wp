# Carrousel Gallery Manager

A comprehensive gallery management plugin for the Carrousel de Paris WordPress theme, featuring drag-and-drop ordering, unlimited media support, and intuitive management interface.

## Features

### 🎨 **Dynamic Media Management**
- Add unlimited images and videos from your WordPress media library
- Support for all common image formats (JPEG, PNG, WebP, GIF)
- Support for video formats (MP4, WebM, OGG)
- Automatic media type detection and proper handling

### 📱 **Drag & Drop Interface**
- Intuitive drag-and-drop ordering with visual feedback
- Real-time reordering without page refresh
- Sortable grid layout that works on all devices
- Visual indicators for drag operations

### ✏️ **Individual Item Control**
- Edit titles and alt text for each gallery item
- Inline editing with expandable panels
- Automatic saving with change detection
- Accessibility-friendly alt text support

### 🔧 **Advanced Features**
- **Keyboard Shortcuts**: Ctrl+S to save, Esc to close panels
- **Auto-save Warning**: Prevents accidental data loss
- **Visual Feedback**: Loading states, success/error messages
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Performance Optimized**: Efficient database queries with proper indexing

### 🌐 **Theme Integration**
- Seamless integration with Carrousel de Paris theme
- Replaces static gallery customizer settings
- Fallback to default images when no custom media is set
- Compatible with existing Glide.js carousel and Lightbox functionality

## Installation

1. **Upload the plugin folder** to `/wp-content/plugins/carrousel-gallery-manager/`
2. **Activate the plugin** through the 'Plugins' menu in WordPress
3. **Navigate to 'Gallery Manager'** in your WordPress admin menu
4. **Start adding media items** to your gallery

## Usage

### Adding Media Items

1. Click the **"Add Media"** button in the Gallery Manager
2. Select images and/or videos from your media library (hold Ctrl/Cmd for multiple selection)
3. Click **"Add to Gallery"** to add selected items
4. Items will appear in your gallery management interface

### Reordering Items

- Simply **drag and drop** gallery items to reorder them
- Use the drag handle (≡ icon) on each item
- The new order will be reflected on your website's gallery
- Remember to click **"Save Gallery"** to persist your changes

### Editing Item Details

1. Click the **edit icon** (pencil) on any gallery item
2. The item details panel will expand below the preview
3. Modify the **title** and **alt text** fields as needed
4. Click **"Save Gallery"** to save your changes

### Removing Items

1. Click the **trash icon** on any gallery item
2. Confirm the removal in the dialog
3. The item will be removed from the gallery (soft deletion)

### Keyboard Shortcuts

- **Ctrl+S** (or **Cmd+S** on Mac): Save gallery
- **Esc**: Close currently open edit panel

## Technical Details

### Database Structure

The plugin creates a custom table `wp_carrousel_gallery` with the following structure:
- `id`: Unique identifier for each gallery item
- `media_id`: WordPress media library attachment ID
- `media_type`: Type of media (image/video)
- `title`: Custom title for the item
- `alt_text`: Alternative text for accessibility
- `sort_order`: Order position in the gallery
- `is_active`: Status flag for soft deletion
- `created_at`: Creation timestamp
- `updated_at`: Last modification timestamp

### Theme Integration

The plugin provides a helper function `cgm_get_gallery_items()` that returns formatted gallery data for theme use. The theme's existing `carrousel_get_gallery_images()` function has been updated to use the plugin data when available, with fallback to default images.

### Frontend Display

Gallery items are displayed using the existing Glide.js carousel with enhanced support for:
- Video playback with custom controls
- Mixed media types in the same gallery
- Responsive behavior for different screen sizes
- Lightbox integration for images
- Video pause/play coordination with gallery autoplay

## API Reference

### Functions

#### `cgm_get_gallery_items()`
Returns an array of gallery items formatted for frontend display.

**Returns:**
```php
array(
    array(
        'id' => 1,
        'media_id' => 123,
        'src' => 'https://example.com/image.jpg',
        'title' => 'Gallery Item Title',
        'alt' => 'Alternative text',
        'type' => 'image', // or 'video'
        'is_video' => false,
        'thumbnail' => 'https://example.com/thumb.jpg'
    ),
    // ... more items
)
```

### AJAX Actions

#### `cgm_save_gallery`
Saves gallery item order and metadata.

#### `cgm_get_media`
Adds new media items to the gallery.

#### `cgm_remove_media`
Removes (soft deletes) gallery items.

## Customization

### Styling

The plugin includes comprehensive CSS styling in `/assets/admin.css`. You can override these styles in your theme or child theme as needed.

### JavaScript Events

The admin JavaScript triggers custom events that you can listen to:
- Gallery items are added
- Items are reordered
- Items are removed
- Gallery is saved

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Changelog

### Version 1.1.0
- Enhanced admin interface with improved UX and visual feedback
- Added keyboard shortcuts support (Ctrl+S to save, Esc to close panels)
- Improved drag-and-drop visual feedback with rotation and shadow effects
- Added item count display in page title and toolbar statistics
- Enhanced error handling and user feedback with better messages
- Better responsive design for mobile devices
- Added comprehensive documentation and help text
- Improved accessibility with better focus management
- Added visual indicators for modified items

### Version 1.0.0
- Initial release
- Dynamic gallery management
- Drag-and-drop ordering
- Mixed media support (images and videos)
- Responsive admin interface
- Theme integration
- Database optimization

## Support

For support and feature requests, please contact the Carrousel de Paris development team.

## License

This plugin is licensed under the GPL v2 or later.
