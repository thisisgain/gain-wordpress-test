# Erebus site template
This is a template for a WP site.

## Example blocks and Field Groups
Within this theme you will find in the ```classes/ACF``` folder - Blocks and FieldGroups directories.
These are here for example only on how to register a new block for Gutenberg along with the field groups for the block.

### Seeing the example blocks in action
To activate the example blocks follow these steps:

1. Update the namespaces in both testimonials classes to match your site namespace
```
Change this:
namespace NanoSoup\Erebus\ACF\Blocks;

To this:
namespace GainTest\ACF\Blocks;
```
2. Add the following code to your functions.php file:
```
$fieldGroups = new \GainTest\ACF\FieldGroups\Testimonials();
$fieldGroups->init();

$testimonials = new \GainTest\ACF\Blocks\Testimonials();
$testimonials->init();
```
You will then be able to select and use the Testimonials block within the Gutenberg editor

# Description

-   What:
-   When: 
-   Where:
    - UAT: [](), 
    - Production: []()
-   Multsite:
-   Tech:

# Setup

## Prerequisites

-   Follow the instructions in the wiki [here]([https://bitbucket.org/strawberrysoup/tj-digital/wiki/Computer%20setup)

## Initial Setup

-   Some of these instructions may still be relevant [here]([https://bitbucket.org/strawberrysoup/tj-digital/wiki/Project%20Setup)
-   run `yarn install` in root directory

## Running the Project

Run `composer install` in the project root, then switch down into the project theme `wp-content/themes/theme_name` and run `yarn install`.

# Updates and Testing

## Testing

# Hosting and Deploying

## Paid Plugins
