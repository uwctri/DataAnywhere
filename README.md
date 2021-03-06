# DataAnywhere - Redcap External Module

## What does it do?

DataAnywhere ships data from other events, specific instruments on other events, or all of a record's data to the current instrument as a javascript object for use by other External Modules such as Shazam. This allows for easy access to the data for use in custom JS.

## Installing

This EM isn't available to install via redcap's EM database so you'll need to install to your modules folder (i.e. `redcap/modules/data_anywhere_v1.0.0`) manually if you'd like to use it.

## Configuration

Configuration is simple: 

1. Define data to share - This can be a specifc event or event/instrument combo or all data on the record.
2. Define where that data is sent - Same specification as above
3. Use data in the `DataAnywhere.data` object, data is organized by event_id and, if applicable, in the typical redcap 'repeat_instances' scheme.

## Call Outs

* You cannot ship data from other records.
