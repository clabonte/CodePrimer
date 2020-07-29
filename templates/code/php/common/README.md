This folder contains the following **Generic Templates** used to generate PHP artifacts.

## Template: `class.php.twig`
Base template to extend by all concrete templates intended to create a PHP class artifact

### Template Variables
The following variables can be set by concrete templates extending this one:
- `getters`: Whether getters should be defined for this class. 
   - Default = **true**
- `setters`: Whether setters should be defined for this class. 
   - Default = **true**
- `fluent`: Whether this class should provide fluent setters; only applicable if 'setters' is true.
   - Default = **true**
- `constructor`: Whether this class should provide a default constructor that includes all mandatory fields.
   - Default = **true**
- `class_modifier`: The PHP modifier associated with this class.
   - Default = **empty**
   
### Blocks
The following blocks can be overridden by concrete templates extending this one:
- `file_comments`: PHPDoc file-level comments
- `namespace`: PHP namespace to use for the generated class
- `includes`: List of classes to include
- `class_comments`: PHPDoc class-level comments
- `fields`: List of fields (aka members) to define for the class
- `constructor`: List of constructors to define *when `constructor` template variable is true*
- `setters_getters`: List of setters and getters to include, using `setters`, `getters` and `fluent` template variable to drive content
- `list_methods`: List of methods used to manage list-typed fields
- `body`: List of custom methods to define for the class

## Template: `interface.php.twig`
Base template to extend by all concrete templates intended to create a PHP interface artifact

### Template Variables
N/A
   
### Blocks
The following blocks can be overridden by concrete templates extending this one:
- `file_comments`: PHPDoc file-level comments
- `includes`: List of classes to include
- `class_comments`: PHPDoc class-level comments
- `body`: List of custom methods to define for the interface

## References
More information on template structure and guidelines can be found [here](../../../../doc/StructureAndGuidelines.md)
