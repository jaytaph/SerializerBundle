# Context

## groups
By default all mappings will assume the `DEFAULT` group. It's possible to add other groups. It's not possible to 
remove the default group: `$context->hasGroup('DEFAULT');` will always return `true`.

## version
When no version is set both `sinceVersion()` and `untilVersion()` will return true, otherwise any version set by 
`setVersion()` will be checked.


