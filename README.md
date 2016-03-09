# SilverStripe Modulator

[![Travis](https://img.shields.io/travis/touchcast/modulator.svg)](https://travis-ci.org/touchcast/modulator)
[![Packagist](https://img.shields.io/packagist/v/touchcast/modulator.svg)](https://packagist.org/packages/touchcast/modulator)
[![Packagist](https://img.shields.io/packagist/dt/touchcast/modulator.svg)](https://packagist.org/packages/touchcast/modulator)

Modulator is an extendable system for creating sub-page components. It achieves this by attaching DataObjects to the page in place of the traditional Content body.

## Features

* Draft and publish control on a per-module level
* Drag-and-drop re-ordering of modules
* An extendable system for creating your own modules
* CMS previewing

## Installation

Modulator can be installed via Composer;

    composer require touchcast/modulator dev-master

After installing, run a ``/dev/build`` to generate the database tables.

## Usage

Create a ``ModularPage`` page within your Site Tree. Add new modules to the page and populate them as required.

## Creating modules

Each module consists of a PHP class and a template file.

Start by extending ``PageModule``

```php
class HeroModule extends PageModule {

  // Give the module a name for use within the CMS
  public static $title = "Hero module";

  // Give it a description
  public static $description = "A large title section at the top of the page";

  // Give it a 64x64px icon image
  public static $icon = "mysite/images/module-hero.png";

  // Add any fields required for the module
  private static $db = array(
    "Heading" => "Varchar(128)"
  );

  // Provide custom summary content for the gridfield
  public function getSummaryContent() {
      return $this->Heading;
  }

  // Provide text content from the module to be included in the pages's search index
  public function getSearchBody() {
      return $this->Heading;
  }
}
```

Next create a template file. It should have the same name as your module class.

```html
<header>
  <h1>$Heading</h1>
</header>
```

If you want to extend the ``ModularPage`` template, you can render the modules by hand using the ``$ActiveModules`` loop.

```html
<% loop $ActiveModules %>
<section class="$ClassName.Lowercase <% if $Odd %>odd<% else %>even<% end_if %> order-$Order $ExtraClasses">
  $Content
</section>
<% end_loop %>
```

## Testing

Run ``phpunit`` from within the module folder, or ``/dev/tests`` from the browser.

