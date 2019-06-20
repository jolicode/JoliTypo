This bundle integrates the [JoliTypo](https://github.com/jolicode/JoliTypo) library into Symfony.

Please refer to the [JoliTypo documentation](https://github.com/jolicode/JoliTypo/blob/master/README.md) to learn more
about fixers and how to combine them.

**Note:** there is no cache involved with JoliTypo, take care of it if you want to save some CPU cycles :grimacing:

Not using Symfony Flex? Register the bundle `JoliTypo\Bridge\Symfony\JoliTypoBundle` in your kernel:

```php
  new JoliTypo\Bridge\Symfony\JoliTypoBundle(),
```

Define your Fixers preset as you want (in `config/packages/joli_typo.yaml` or `app/config/config.yml`):

```yaml
joli_typo:
    presets:
        fr:
            fixers: [ Ellipsis, Dimension, Dash, SmartQuotes, FrenchNoBreakSpace, CurlyQuote, Trademark ]
            locale: fr_FR
        en:
            fixers: [ Ellipsis, Dimension, Dash, SmartQuotes, CurlyQuote, Trademark ]
            locale: en_GB
```

Twig function
=============

The Bundle makes use of the Twig bridge and it's method/filter named `jolitypo`, waiting for two arguments: HTML content
to fix and the preset name.

```twig
{{ jolitypo('<p>Hi folk!</p>', 'fr') | raw }}

{# or #}

{{ '<p>Hi folk!</p>' | jolitypo('fr') }}
```

To fix large HTML content, you can pass the markup directly into the `filter` (Twig version < 2.8) / `apply` (Twig version >= 2.8) tag:

```twig
{# Twig < 2.8 #}
{% filter jolitypo('fr') %}
    <p>Je suis "très content" de t'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>
    <p>12 x 6 = 72</p>
    <p>(r), (c), (TM)</p>
    <p>"Tell me Mr. Anderson... what good is a phone call... if you're unable to speak?" -- Agent Smith,<em>Matrix</em>.</p>
{% endfilter %}
```

```twig
{# Twig >= 2.8 #}
{% apply jolitypo('fr') %}
    <p>Je suis "très content" de t'avoir invité sur <a href="http://jolicode.com/">Jolicode.com</a> !</p>
    <p>12 x 6 = 72</p>
    <p>(r), (c), (TM)</p>
    <p>"Tell me Mr. Anderson... what good is a phone call... if you're unable to speak?" -- Agent Smith,<em>Matrix</em>.</p>
{% endapply %}
```

Another way to use it is by passing a whole block to it:

```twig
{% block content %}
    {{ jolitypo(block('real_content'), 'fr') | raw }}
{% endblock %}

{% block real_content %}
    <h2>My whole dynamic page</h2>
{% endblock %}
```
