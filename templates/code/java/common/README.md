This folder contains the following **Generic Templates** used to generate Java artifacts:

## Template: `class.java.twig`
Base template to extend by all concrete templates intended to create a Java class artifact

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
- `class_scope`: The scope associated with this class.
   - Default = **public**
   
### Blocks
The following blocks can be overridden by concrete templates extending this one:
- `file_comments`: Javadoc file-level comments
- `imports`: List of classes to import
- `class_comments`: Javadoc class-level comments
- `fields`: List of fields (aka members) to define for the class
- `constructor`: List of constructors to define *when `constructor` template variable is true*
- `setters_getters`: List of setters and getters to include, using `setters`, `getters` and `fluent` template variable to drive content
- `body`: List of custom methods to define for the class

## References
More information on template structure and guidelines can be found [here](../../../../doc/StructureAndGuidelines.md)
