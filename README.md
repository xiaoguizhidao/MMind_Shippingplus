MMind_Shippingplus
==================

Shipping Module for Magento.

# Features
- shipping price with tablerate weight/destination
- shipping price with tablerate price/destination
- shipping flat price (based on cart total or weight cart total)
- free shipping
- add import tablerate with multiple country, region, postcode divided by pipe '|'

## Tablerate

The module search in order: postcode, region, country.
So if you have particular price for postcode they must be at the beginning of the tablerate.
See tablerate_example.csv for more informations

# Contributors
- Giuseppe Morelli

# Stable version

1.0.1
- add import tablerate with multiple country, region, postcode divided by pipe '|'

1.0.0
- first release

# MAGENTO Installation

### via [modman](https://github.com/colinmollenhour/modman):
<pre>
modman clone git@github.com:magemindcom/MMind_Shippingplus.git  
</pre>

### via [composer](https://getcomposer.org/download/)
Add to your composer.json file this:
<pre>
{
    ...
    "require": {
        "magento-hackathon/magento-composer-installer": "*",
        "mmind/shippingplus": "1.0.0"
    },
    ....
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/magemindcom/MMind_Shippingplus"
        }
    ],
    .....
}</pre>