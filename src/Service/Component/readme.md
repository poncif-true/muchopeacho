# Twig Component Renderer

## How it works
The class uses `Twig_Environnement` to render a template with context.  
It allows you to define a component, which MUST implement `Renderable`. So that you can put your component HTML (and CSS and JS) inside a single twig template.  

You MUST put the component's template inside your project templates directory, as Twig will look for it the same way that it goes inside a `Controller`.

## How to use it
Let's say you have a Component : 
```php
# Component.php

class Component implements Renderable
{
    protected $property;
    protected $anotherProperty;
    
    public function __construct($property, $anotherProperty)
    {
        $this->property = $property;
        $this->anotherProperty = $anotherProperty;
    }
    
    public function getTemplate()
    {
        return 'path/to/template.html.twig';
    }
    
    public function getContext()
    {
        return [
            'property' => $this->property,
            'anotherProperty' => $this->anotherProperty,
        ];
    }
```

the Component template :
```twig
# path/to/template.html.twig

<p>Hello world !</p>
<p>I know about {{ property }} and {{ anotherProperty }}</p>
```

then inside your Controller : 
```php
# MyController.php

public function Action(TwigComponentRenderer $renderer)
{
    // do some things
    $component = new Component('maths', 'birds');
    
    return $this->render(
        'my_controller_template.html.twig',
        [
            'renderer' => $renderer,
            'component' => $component,
        ]
    );
}
```

and finally in your page template
```twig
# my_controller_template.html.twig

{% extends 'base.html.twig' %}

<h1>What does say the component ?</h1>
{{ renderer.render(component)|raw }}

```