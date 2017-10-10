# FinalPHP Framework and Library Collection

FinalPHP is repository containing frameworks and libraries used for
building PHP backends.

This source includes: (currently; list to be appeneded to)
- Nano Framework - A small microframework using Aura router and YAML parsing
- Class L - A library containing language tools,
  many of which are inspired by Golang.

## Definitions
| Term | Description|
| ---- | ---- |
| Project Root | This is a hypothetical directory containing a new project using FinalPHP.

## Installation
The recommended way to install FinalPHP is using Composer. For information regarding Composer, see `getcomposer.org`.

### Installation From Repository
Add the text below to a new file called `composer.json` in the project root, then run `composer install` in the same directory.

```json
{
  "autoload": {
    "psr-4": {
      "":"src/"
    }
  },
  "repositories":[
    {
      "type": "vcs",
      "url": "git@github.com:KernelDeimos/FinalPHP.git"
    }
  ],
  "require": {
    "dubedev/finalphp": "dev-master"
  }
}
```
  
This should work as is, but sometimes it can be problematic. If this does not work, refer to the next section.

### Possible Issue
For unknown reasons, Composer will somtimes fail to use the SSH key configured on the system, yeilding a an `Auth` or `404` error. If this is the case, running `composer install -n` may install successfully.

## Usage

### New Project with NanoFrameowrk
NanoFramework is a microframework providing a conventional but very simple
project structure. It uses Aura.Router and Symfony's YAML component, as well as
Class L (a set of language utilities available in FinalPHP). To create a project using this framework,
refer to the steps following.

#### Step 1: Create index.php

Add the following code text to the main source file (ex: `index.php`)

```php
require('vendor/autoload.php');
use \FinalPHP\Frameworks\Nano;

$f = Nano\NanoFramework::NewWithConfigFiles("./nano.yml");

{
    $r = $f->get_router();
    $r->GET("index.read", "/", "Index");
}	

$f->go();
```

**Note:** The NewWithConfigFiles constructor is a variadic function, and allows
specification of multiple configuration files.
Parameters in a subsequent file may override those parameters of any preceding file.

#### Step 2: Create nano.yml

Add the following text to a new file named `nano.yml` in the project root.

```yaml
mode: test
router:
  base_path: /
errors:
  errors_to_exceptions: true
```

**Note:** It is necessary base_path to a different value if the project
is not under the web root. For example, if the project is to be accessed at
`devserver.com/myproject`, the value of base_path should be `myproject/`.

#### Step 3: Create Index Controller

Create a new file called `Index.php` in the directory `src/Controllers` relative
to the project root.

```php
<?php

namespace Controllers;

class Index
{
    function handler($c, $api)
    {
        echo "It Works";
    }
}
```

#### Step 4: Test Setup

Visit the URL corresponding to the project root to ensure the framework has been
setup correctly. For example, if the project is under `~/public_html/myproject`,
the corresponding URL may be `http://127.0.0.1/myproject`.

### Frameworks/Nano: Adding Tools
This framework makes it easy to make components or data available to all
controllers by adding it to the frameowork as a tool. The following example
adds an instance of the Twig template engine from Symfony to NanoFramework.

#### Example 1: Adding Twig to Framework
```php
// ... <index.php>
$f = Nano\NanoFramework::NewWithConfigFiles("./nano.yml", "./server.yml");

{
    $loader = new Twig_Loader_Filesystem('./templates');
    $twig = new Twig_Environment($loader);
    $f->add_tool("twig", $twig);
}
// ...
```
#### Example 2: Using Twig in Controller
```php
// ... <src/Controllers/Index.php>
class Index
{
    function handler($c, $capi)
    {
        $tmpl = $api->tools['twig']->load('templates/index.html.twig');
        echo $tmpl->render();
    }
}
```
### Frameworks/Nano: Adding Sandwichwares
Sandwichware serves the same purpose as middleware in other frameworks. These
components extend the request handler, and can do anything a controller can do
before the controller is invoked.

Unlike middleware, a sandwichware doesn't invoke the next handler. Instead, the
router component invokes a before_handler method on each registered sandwichware,
then calls the controller, then invokes an after_handler method on each registered
sandwichware in reverse order.

The following is an example definition of a sandwichware that records the controller
execution time as a comment at the bottom of the response.

#### New file: src/Handlers/Time.php
```php
<?php

namespace Handlers;

class Time
{
    function __construct()
    {
        $this->startTS = NULL;
        $this->stopTS = NULL;
    }
    function before_handler($c, $api)
    {
        $this->startTS = microtime(true);
    }
    function after_handler($c, $api)
    {
        $this->endTS = microtime(true);
        echo "<!-- TIME ";
        echo $this->endTS - $this->startTS;
        echo "-->";
    }
}
```

To include this sandwichware, it can be added directly to the router as
demonstrated.

#### Addition to: index.php
```php
// ... <index.php>
{
    $r = $f->get_router();
    $r->add_sandwichware(new \Handlers\Time());
}
// ...
```

Loading any page should now display the amount of time taken to execute the
controller, as described earlier.

### Using Templates
FinalPHP provides classes under `\FinalPHP\Tmpl` that implement various
template functionalities. Each sub-path of this namespace is described
below.

#### \FinalPHP\Tmpl\IncludeTmpl
One of the simplest ways to make a template is using raw PHP. This is considered
a traditional approach, but it is supported in many frameworks including FinalPHP.

Using this kind of template is almost as simple as calling `include()`.
The convenient `Template` class provided keeps all template-related values in one place.

Below is an example of a controller that serves a `homepage.php` file located
inside a `templates/` directory under the project root.

```php
<?php

namespace Controllers;

use FinalPHP\Tmpl\IncludeTmpl\Template as IncTemplate;

class Index
{
    function handler($c, $api)
    {
        $tmpl = new IncTemplate("templates/homepage.php");
        $tmpl->title = "Hello World Page";
        $tmpl->text = "Hello, World!";
        $tmpl->Render();
    }
}
```

The document under `templates/homepage.php` might look like this:
```html
<html>
    <head>
        <title><?php echo $title; ?></title>
    </head>
    <body><?php echo $text; ?></body>
</html>
```

#### \FinalPHP\Tmpl\PHPTmpl
This package implements templates using HTML-generation functions in PHP.
This will be further documented soon.

#### \FinalPHP\Tmpl\Base
This namespace contains one class, `Template`. This is an abstract class which
provides a public method `->Render()`. The `Template` class of any other
subpackage of `\FinalPHP\Tmpl` will extend this class.

Any `Template` object also overrides the magic `__set()` method as a convenient
way to set variables that will be available to the template.
