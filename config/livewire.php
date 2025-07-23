<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Class Namespace
    |---------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes in
    | your application. This value will change where component auto-discovery
    | finds components. It's also referenced by the file creation commands.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |---------------------------------------------------------------------------
    | View Path
    |---------------------------------------------------------------------------
    |
    | This value is used to specify where Livewire component Blade templates are
    | stored when running file creation commands like `artisan make:livewire`.
    | It is also used if you choose to omit a component's render() method.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |---------------------------------------------------------------------------
    | Layout
    |---------------------------------------------------------------------------
    | The view that will be used as the layout when rendering a single component
    | as an entire page via `Route::get('/post/create', CreatePost::class);`.
    | In this case, the view returned by CreatePost will render into $slot.
    |
    */

    'layout' => 'components.layouts.app',

    /*
    |---------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |---------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    | Livewire handles file uploads by storing uploads in a temporary directory
    | before the file is stored permanently. All file uploads are directed to
    | a global endpoint for temporary storage. You may configure this below:
    |
    */

    'temporary_file_upload' => [
        'disk' => null,        // Example: 'local', 's3'              Default: 'default'
        'rules' => null,       // Example: ['file', 'mimes:png,jpg']  Default: ['required', 'file', 'max:12288'] (12MB)
        'directory' => null,   // Example: 'tmp'                      Default: 'livewire-tmp'
        'middleware' => null,  // Example: 'throttle:5,1'             Default: 'throttle:5,1'
        'preview_mimes' => [   // Supported file types for temporary pre-signed file URLs...
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5, // Max duration (in minutes) before an upload is invalidated...
    ],

    /*
    |---------------------------------------------------------------------------
    | Render On Redirect
    |---------------------------------------------------------------------------
    | This value determines if Livewire will run a component's `render()` method
    | after a redirect has been triggered using something like `redirect(...)`
    | If this is disabled, the render method will only run on the next request.
    |
    */

    'render_on_redirect' => false,

    /*
    |---------------------------------------------------------------------------
    | Eloquent Model Binding
    |---------------------------------------------------------------------------
    | Previous versions of Livewire supported binding directly to eloquent model
    | properties using wire:model on custom components. However, this approach
    | was vulnerable to "mass assignment" attacks. This feature is now opt-in.
    |
    */

    'legacy_model_binding' => false,

    /*
    |---------------------------------------------------------------------------
    | Auto-inject Frontend Assets
    |---------------------------------------------------------------------------
    | By default, Livewire automatically injects its JavaScript and CSS into the
    | <head> and before the closing </body> tag of pages containing Livewire
    | components. By disabling this, you take full control of the asset inclusion.
    |
    */

    'inject_assets' => true,

    /*
    |---------------------------------------------------------------------------
    | Navigate (SPA mode)
    |---------------------------------------------------------------------------
    | By default, page navigation in Livewire uses full page reloads. If you
    | want to use Livewire's SPA-mode navigation, you can enable it here.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |---------------------------------------------------------------------------
    | HTML Morphing
    |---------------------------------------------------------------------------
    | Livewire uses a simplified diff to determine what has changed in a page
    | when it needs to re-render. Sometimes, complex pages need help to reduce
    | the time it takes to calculate what changed. Here are additional options.
    |
    */

    'morphing' => [
        'morphdom' => [
            'onBeforeElUpdated' => null,
        ],
    ],

    /*
    |---------------------------------------------------------------------------
    | Asset URL
    |---------------------------------------------------------------------------
    | This value sets the path to Livewire JavaScript assets, for when you can't
    | use the default route that Livewire provides or you want to serve them
    | from a CDN. Please keep in mind that this must be a full URL.
    |
    */

    'asset_url' => env('LIVEWIRE_ASSET_URL', null),

    /*
    |---------------------------------------------------------------------------
    | App URL
    |---------------------------------------------------------------------------
    | This value should be set to the root URL of your application so that
    | Livewire can generate URLs to your application's pages.
    |
    */

    'app_url' => env('APP_URL', null),

    /*
    |---------------------------------------------------------------------------
    | Middleware
    |---------------------------------------------------------------------------
    | This value sets the middleware that will be applied to all of Livewire's
    | routes (including routes auto-registered by Livewire components with
    | `#[Lazy]` attributes, etc.)
    |
    */

    'middleware' => [
        'web',
        \Livewire\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware::class,
    ],

    /*
    |---------------------------------------------------------------------------
    | Manifest Path
    |---------------------------------------------------------------------------
    | This value sets the path to the Livewire asset manifest inside public path,
    | for when you want the published assets to be served from a subdirectory.
    |
    */

    'manifest_path' => env('LIVEWIRE_MANIFEST_PATH', null),

]; 