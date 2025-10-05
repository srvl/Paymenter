# Changelog v1.1.1
- streamline country field handling
- add email_verified configuration

# Changelog v1.1

## New
- **Configurable Field System**: Added centralized field configuration system for eligibility conditions
- **Dynamic Form Generation**: Filament forms now automatically generate based on field configuration
- **Field Validation**: Added automatic operator validation for each field type
- **Value Mapping**: Support for dropdown options with readable labels (e.g., day names)

## Improvements
- **Code Maintainability**: Removed hardcoded field definitions and mappings
- **Extensibility**: Easy to add new fields without touching multiple files
- **Consistency**: Unified field handling across model and form components
- **Better UX**: Smarter form inputs with appropriate placeholders and validation

## Technical Changes
- Refactored `RedeemifyCode` model with configurable field system
- Added `getFieldConfig()`, `getOperatorConfig()`, and helper methods
- Updated Filament form to use dynamic field configuration
- Improved field value resolution and condition evaluation

## Developer Experience
- Single source of truth for field definitions
- Simplified process for adding new eligibility fields
- Better code organization and separation of concerns