# Mathematical Field

The Mathematical Field module gives you the ability to type mathematical calculations into a plain text field and have its result displayed.

For example, entering the calculation `10 + 20 - 30 + 15 * 5` would return the result `75`.

The module comes with various ways to display this result, more information about this can be found under the `Display Options` section.


## Install

Installing this module is much like any other Drupal module:

1. Download / clone this repo.
2. Move the folder so it is under `modules/contrib`.
3. On Drupal navigate to Extend (`/admin/modules`).
4. Search for `Mathematical Field` and enable it.

You are now ready to use the Mathematical Field module!


## Usage

Once installed (see `Install` section), using the module is very easy.

This module does not create its own field type instead it extends the `Text (plain)` & `Text (plain, long)` fields, adding in a new format for display called `Mathematical`.

You can add the field in 3 easy steps:

1. Chose the content type you would like to add a `Mathemtical Field` too.
2. Add a new field of either `Text (plain)` & `Text (plain, long)`
3. On the `Manage display` tab set the new fields format to `Mathematical`.

That's it! Now the new field is ready for mathematical calculating.

You can also upgrade any existing `Text (plain)` & `Text (plain, long)` by simply following step 3 of usage.

## Display Options