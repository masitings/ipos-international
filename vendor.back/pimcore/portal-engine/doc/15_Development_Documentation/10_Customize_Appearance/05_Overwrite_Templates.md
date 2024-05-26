# Overwrite Templates

## React components

Most of the HTML markup of the portal engine frontend is rendered via client side rendering (React / JSX components).
Take a look at the [Customized Frontend Build](./10_Customize_Frontend_Build.md) and
[Customize JSX Components](./20_JSX_Components.md) section for a guide how to overwrite the corresponding JavaScript files.

## Twig templates

Nevertheless, the portal engine also ships with a bunch of Twig templates located in `src/Resources/views`. The mechanism 
to overwrite them is just the standard Symfony way of doing this which is described 
[here](https://symfony.com/doc/current/bundles/override.html#templates).

At certain places the portal engine adds Twig `{% block %}` statements which makes it possible to overwrite parts of the 
templates only. Take a look at the [Twig docs](https://twig.symfony.com/doc/3.x/tags/extends.html) for more details.


### Example

Extend the login template, remove the logo + background image and wrap the headline into a `<div>`.

```twig
# needs to be located at app/Resources/PimcorePortalEngineBundle/views/auth/login.html.twig
{% extends '@!PimcorePortalEngine/auth/login.html.twig' %}

{% block logo %}
{% endblock %}

{% block login_background %}
{% endblock %}

{% block headline %}
    <div class="text-primary">
        {{ parent() }}
    </div>
{% endblock %}
```

 
 