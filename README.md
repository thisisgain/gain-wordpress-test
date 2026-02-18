# GAIN WordPress Test

## Overview

This exercise is a short, WordPress-focused technical test.

You will:

- Run a small WordPress installation locally.
- Debug and fix a minor PHP error in a custom ACF component
- Apply suitable styling to the ContentWithImage ACF block using the `wp-content/themes/gain-test/assets/stylesheets/components/_content-with-image.scss` file to reflect the available ACF fields for the block, i.e. `Background colour` & `Flip content` options.
- Add a new ACF field to the block which allows the user to add a CTA button with link and text options, and then update the block template to render this new field.
- Open a pull request, describing your changes.

_Estimated time to complete:_ 1.5-2 hours

## Prerequisites

You'll need:

- PHP 8.2+
- Composer 2
- Git
- Node v18.20.7+
- Yarn v1.22.22+
- Laravel Valet or similar local development environment (e.g. MAMP/XAMPP) for running the WordPress installation locally

## Setup

### Create your repository

IMPORTANT: Use this template to create your own private repository:

1. Click "Use this template" → "Create a new repository"
2. Make it private
3. Name it gain-wordpress-test-[your-name]
4. Clone your new repository:

```bash
git clone <YOUR_REPO_URL> gain-wordpress-test
cd gain-wordpress-test
```

5. Add the reviewer as a collaborator to your private repository

### Environment Setup

In the root of your project, run the following command to install the project dependencies managed by composer for the WordPress project:

```bash
composer install
```

Add the following (with your own values) to a `.env` file in the root of your project (make sure to replace the database credentials with your own values, and ensure the `WP_HOME` and `WP_SITEURL` values match the URL you will be using to access the site locally):

```
WP_HOME="https://gain-wordpress-test.test"
WP_SITEURL="https://gain-wordpress-test.test"
DB_NAME="wordpress_test"
DB_USER="db_user"
DB_PASSWORD="db_password"
DB_HOST="127.0.0.1"
DB_CHARSET="utf8"
DB_COLLATE=""
WP_DEBUG="false"
```

Please use the supplied SQL file (shared with you separately) as the database for the project, as this has the test page setup and ready for you to work on.

To create an admin user, use the WP CLI command (assuming you have this installed already) and change the USERNAME, EMAIL and PASSWORD values as needed:

```bash
wp user create USERNAME EMAIL --role=administrator --user_pass="PASSWORD"
```

Finally, for frontend assets, we use webpack within the theme folder to compile our SCSS and JS files. To install the necessary node modules and run the asset pipeline, navigate to the theme folder and run the following commands:

```bash
cd wp-content/themes/gain-test
yarn install
yarn watch
```

Note: You will need to have the `yarn watch` command running in order to see your CSS changes reflected on the frontend, as this will compile the SCSS files into CSS whenever you make a change. If you create a new SCSS file, make sure to stop the watch command and start it again to ensure the new file is included in the compilation.

## Tasks

Please complete the following tasks:

### Task 1 - Debug & bugfix

There is an intentional minor PHP error in the `ContentWithImage` ACF block component. Please debug and fix this error when visiting the example hello world post = `https://wordpress-test.test/hello-world/` (replace/update the url as needed based on your local setup).

### Task 2 - Styling of ContentWithImage block

Using the example post 'Hello World' - Apply suitable styling to the ContentWithImage ACF block using the `wp-content/themes/gain-test/assets/stylesheets/components/_content-with-image.scss` file to reflect the available ACF fields for the block, i.e. `Background colour` & `Flip content` options.

Where possible, try and utilise SCSS methods/variables and BEM naming when writing your styles, and ensure that the block is styled to be responsive, ideally using CSS grid or flexbox in a mobile-first approach.

### Task 3 - Add ACF field for CTA button

Finally, add a new ACF field to the block which allows the user to add a CTA button with link and text options, and then update the block template to render this new field.

## What we look at

When reviewing your PR, we'll mainly look at:

- Correctness – does the block and styling behave as requested?
- Code quality – structure, naming, readability, basic comments where helpful.
- Support mindset – does your bugfix change clearly solve the issue and avoid obvious regression?
- Git & communication – clear commit messages and a short PR description.

## How to submit

1. Create a new branch from master:

```bash
git checkout -b feature/your-name-solution
```

2. Commit your changes with clear messages i.e. (note each task should sit in its own commit, so you may have multiple commits for each task):

```bash
git commit -am "Fix PHP error in ContentWithImage block"
```

3. Push your branch to your remote repository:

```bash
git push origin feature/your-name-solution
```

4. Open a Pull Request in your own repository:

- Title: Tech test solution – Your Name
- Description:
    - Briefly describe what you changed for Task 1, 2 & 3.
    - Note anything you'd do differently with more time, or any assumptions you had to make.

5. Notify the reviewer that your PR is ready for review.

That's it — thank you for taking the time to complete this exercise.
