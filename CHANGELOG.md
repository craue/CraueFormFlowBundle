# Changelog

## 2.0.0 (not yet released)

* BC break: reworked the way steps are defined (follow `UPGRADE-2.0.md` to upgrade)
* added `PostBindFlowEvent`

## 1.1.3 (2013-05-23)

* added method `getStorage`
* made the dependency on an event dispatcher optional

## 1.1.2 (2013-04-17)

* always dispatch `PreBindEvent` when `bind` is called (to match expected behavior)
* added Brazilian Portuguese translation

## 1.1.1 (2013-04-14)

* avoid skipping all steps by tampering with the hidden step field
* added some basic tests

## 1.1.0 (2013-02-28)

* adjustments to changes in the Form component for Symfony 2.1.*
* adjustments to changes in the HttpFoundation component for Symfony 2.1.*
* added `StorageInterface`
* added route parameters to links generated for dynamic step navigation
* preserve given `validation_groups` option
* added the flow instance as a property in events
* throw an exception if the number of steps doesn't match the number of step descriptions

## 1.0.0 (2012-03-07)

* first stable release for Symfony 2.0.*

## 2011-08-02

* initial commit
