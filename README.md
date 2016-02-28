# SilverStripe Modulator

Modulator is an extendable system for creating sub-page components. It achieves this by attaching DataObjects to the page in place of the traditional Content body.

## Features

* Draft and publish control on a per-module level
* Drag-and-drop re-ordering of modules
* An extendable system for creating your own modules

## Installation

Modulator can be installed via Composer;

    composer require "touchcast/silverstripe-modulator" "dev-master"

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
}
```

Next create a template file. It should have the same name as your module class.

```html
<header>
  <h1>$Heading</h1>
</header>
```

## Testing

Coming soon.

