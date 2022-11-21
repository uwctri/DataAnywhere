# DataAnywhere - Redcap External Module

## What does it do?

DataAnywhere ships data from other events, specific instruments on other events, or all of a record's data to the current instrument as a javascript object for use by other External Modules such as Shazam. This allows for easy access to the data for use in customizations.

Configuration & Usage is simple:

1. Define data to share - This can be a specifc event or event/instrument combo or all data on the record.
2. Define where that data is sent - Same specification as above
3. Use data in the `DataAnywhere.data` object, data is organized by event_id and, if applicable, in the typical redcap 'repeat_instances' scheme.
4. The `DataAnywhere.eventNames` and `DataAnywhere.usernames` object also exist to help with accessing the correct event_id or displaying a user's name in your customization

## Installing

You can install the module from the REDCap EM repo or drop it directly in your modules folder (i.e. `redcap/modules/data_anywhere_v1.0.0`).
