# Mathematical Field

The Mathematical Field module gives you the ability to type mathematical calculations into a plain text field and have its result displayed.

For example, entering the calculation `10 + 20 - 30 + 15 * 5` would return the result `75`.

This module can currently handle the following inputs:

- Whole numbers e.g 0,1,2,3,4
- Negative numbers e.g. -1,-2,-3,-4,-5
- Decimal numbers e.g. 1.0,1.1,1.2,1.3,1.4,1.5

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

There are two display options within this module.

1. Mathematical - This is the simplest display option, it can be as simple as just showing the result or as complex as showing input, result number, result words and display result on hover.  
2. Mathematical (Jumble) - This outputs the field as part of a React application and gives you the ability to change the equation using the values from the set input.

### Mathematical

The Mathematical format will display your calculation result and has options to to show more information.

Settings for the field formatter can be found on the `Manage display` page and include the following:

1. `Display input` - As well as showing the result you can display the calculation.
2. `Display in words` - As well as showing the result as a number you can display it as a word.
3. `Hover for results` - Instead of just showing the result as a number it will display the calculation and on hover show the result.

![Mathematical Example](https://imgur.com/NYlnvOk)

![Mathematical Hover Example](https://media.giphy.com/media/JROqIgxwwJArHoHnMQ/giphy.gif)


### Mathematical (Jumble)

The Mathematical (Jumble) format gives a more interactive way to display the calculation, with the ability to move numbers and operators around to see what happens to the result in real time.

There are currently now settings for this formatter.

![Mathematical Jumble Example](https://media.giphy.com/media/TL6epnb9u2QZSWe9mo/giphy.gif)


## Service

This module contains two services which if so required can be used in 3rd-party applications.

The Lexer, which converts a calculation into a format that the Parser can use to work out the result, using the Lexer can do the following, tokenize the string, sorting the precedence and convert from infix to postfix.

The Parser, uses the Lexer and then takes this information to return a result.


### Lexer

To use the Lexer you can use the following:

```php

$string = "1 + 1";

$mathematicalLexer = \Drupal::service('mathematical_field.lexer');

$tokenize = $mathematicalLexer->tokenizer($string);
$sorted = $tokenize->sortPrecedence();
$postfix = $sorted->getPostfix()->getResultString();

print $postfix; // returns 11+

```

### Parser

To calculate results from string you can simply use:

```php

$input = "1 + 1";

$mathematicalParser = \Drupal::service('mathematical_field.parser');

try {
  // get the result
  $result = $mathematicalParser->calculate($input)->getResult();
} catch (\Exception $e) {
  // if error set the error variable and result to the error message
  $result = $this->t($e->getMessage());
}

print $result; // returns 2

```
