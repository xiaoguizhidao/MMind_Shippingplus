MMind_Shippingplus
==================
Custom Shipping Method for Magento 1.x CE

## Guide/Documentation
https://github.com/magemindcom/MMind_Shippingplus/wiki

## Features
- shipping price with tablerate weight/destination
- shipping price with tablerate price/destination
- shipping flat price (based on cart total or weight cart total)
- free shipping
- add import tablerate with multiple country, region, postcode divided by pipe '|'

### Tablerate

The module search in order: postcode, region, country.
So if you have particular price for postcode they must be at the beginning of the tablerate.
See tablerate_example.csv for more informations

## Stable version
1.0.1-p2

## Magento version
- 1.7.x
- 1.8.x
- 1.9.x (last 1.9.2.1)

## Requirements
none

## Install via
- composer
- modman

## Composer code
mmind/shippingplus

## Contributors
- Giuseppe Morelli