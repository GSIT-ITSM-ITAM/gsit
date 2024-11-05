# Specifications of definitions

## Introduction

The definition of a class is used for search, display and update the items.

## Specifications


Mandatory fields:

* id: integer | the id used in displaypreferences (got from old version of GSIT / GLPI)
* title: string | the title of the field, with translation
* type: string | values are:
  * input
  * inputpassword
  * datetime
  * dropdown
  * dropdown_remote
* name: string | the name of the field for all types, except for dropdown* we use the name of the function in model (relationship)

Optional fields:

* dbname: string | database field in case of dropdown one to many or many to many relationship
* itemtype: string | the model class name, used for type 'dropdown_remote'
* multiple: boolean | if true, set it, if false, remove the field. This is required when can have multiple values
* pivot: array | the field and value of a pivot table if required for dropdown_remote
* values: array | only for type 'dropdown', can define the values (see next chapter)
* readonly: readonly | field in readonly mode
* displaygroup: name of panel (group many fields in same panel)
* fillable: true if database can be filled with these field (directly from form),
* display: display or not in form
* relationfields ['id', 'name', 'user.id', 'user.completename']
* usein: ['search', 'form', 'notification']


## Case of values of a static list (dropdown)

In the case you use a static list, like tickets status, this is the specifications of the values, the key of array is the key that will be stored in database.

Mandatory:

* title: string | the title name

Optional:

* color: string | the color of field / cell. The list is :
  * red
  * orange
  * yellow
  * olive
  * green
  * teal
  * blue
  * violet
  * purple
  * pink
  * brown
  * grey
  * black
  * or a CSS classname if defined

* icon: string | the icon of the value, choose [here](https://fomantic-ui.com/elements/icon.html)
* displaystyle: string | when you have color, you can set marked, else it's plain color in search cell and no need this field

Example:

```php
$data = [
  5 => [
    'title' => $translator->translate('Very high'),
  ]
];
```