WP Theme Helper
==================
A set of helper classes for WordPress theme development.  Provides helpers for theme settings, rendering files, handling paths, and creating shortcodes, among other things.

Useage
------
This project consists of multiple classes that each provide a set of functionality.  They can be used independently, or a WPThemeHelper object can be used as a manager for them.  These are the classes

### SettingHelper
Use this object to manage WordPress theme settings, providing a simple interface to set all basic theme settings through a single interface without needing to use the various different methods needed to set them in WordPress.  If set initially, it will also run them at the correct point in the WordPress initialization cycle.

Example:

```php
$settingsHelper = new TJM\WPThemeHelper\SettingHelper(Array(
	'settings'=> Array(
		'automatic-feed-links'=> true
		,'custom-header'=> Array(
			'default-image'=> ''
			,'default-text-color'=> '000000'
			,'flex-height'=> true
			,'flex-width'=> true
			,'height'=> 250
			,'max-width'=> 2000
			,'random-default'=> false
			,'width'=> 960
		)
		,'nav-menus'=> Array(
			'footer'=> 'Footer'
			,'header'=> 'Header'
		)
		,'post-thumbnail-size'=> Array(625, 9999)
		,'widget-areas'=> Array(
			Array(
				'name'=> 'Aside 1'
				,'id'=> 'aside-1'
				,'before_widget'=> '<div id="%1$s" class="widget %2$s">'
				,'after_widget'=> '</div>'
				,'before_title'=> '<h3 class="widget-title">'
				,'after_title'=> '</h3>'
			)
			,Array(
				'name'=> 'Aside 2'
				,'id'=> 'aside-2'
				,'before_widget'=> '<div id="%1$s" class="widget %2$s">'
				,'after_widget'=> '</div>'
				,'before_title'=> '<h3 class="widget-title">'
				,'after_title'=> '</h3>'
			)
		)
	)
));
```

By default, the settings override the defaults of the helper.

### Renderer
Uses my [Buffer Manager](https://github.com/tobymackenzie/PHP-BufferManager) to render template files and pass data to them.

Example:

```php
$renderer = new TJM\WPThemeHelper\Renderer();
echo $renderer->render('aboutBox.php', Array(
	'name'=> 'Toby Mackenzie'
	,'description'=> 'Ohio web developer'
));
```

```php
// {theme}/aboutBox.php
<div class="aboutBox">
	<div class="aboutBoxName"><?php echo $name; ?></div>
	<div class="aboutBoxDescription"><?php echo $description; ?></div>
</div>
```

If using a child theme, it will use the PathHelper to check both the child and parent theme for the file.  There is a `renderPiece()` method that runs `render()` for files in a 'pieces' folder.

### PathHelper
Has a `getThemeFilePath($name, $container, $extension)` method that gets the path to a theme file, with child theme files overriding parent theme files.  `$name` is the file name, `$container` is an optional folder path the file is in, and `$extension` is an optional extension to add to the file name.

### ShortcodeHelper
Simple helper for adding shortcodes.  You can add a single shortcode at a time, like:

```php
$shortcodeHelper = new TJM\WPThemeHelper\ShortcodeHelper();
$shortcodeHelper->add('hello', function($attributes, $content=null){ return "Hello {$content}"; });
```
Or pass an array with multiple shortcodes:

```php
$shortcodeHelper->add(Array(
	'goodbye'=> function($attributes, $content=null){ return "Goodbye {$content}"; }
	,'wrap'=> function($attributes, $content=null){
		$elm = (isset($attributes['element'])) ? $attributes['element'] : 'div';
		return "<{$elm}>{$content}</{$elm}>"
	}
));
```

Also has a `get($codes)` method to get the callables of one or more shortcodes if you want to use them elsewhere.

### DataHelper
A simple class for storing data for later access, with `get($key)`, `set($key, $value)`, and `has($key)` methods.
