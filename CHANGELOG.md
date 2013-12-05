# Changelog

## 2.1.4 (2013-12-05)

- adjusted Composer constraint to allow being used with SensioFrameworkExtraBundle 3.0

## 2.1.3 (2013-11-18)

- [#94]: disallow invalid step config options
- ensure that `Step#isSkipped` always returns a boolean value
- avoid triggering deprecation errors when used with Symfony 2.1.x
- [#100]: fixed the step list to avoid linking not yet accessible steps

[#94]: https://github.com/craue/CraueFormFlowBundle/issues/94
[#100]: https://github.com/craue/CraueFormFlowBundle/issues/100

## 2.1.2 (2013-09-26)

- [#90]: fixed the step list to render the last step already been visited (but not submitted) as a link

[#90]: https://github.com/craue/CraueFormFlowBundle/issues/90

## 2.1.1 (2013-09-24)

- ensure that `skip` callables always return a boolean value
- [#87]: the step parameter used in links to specific steps is not limited to be a query parameter anymore, e.g. can be a route parameter

[#87]: https://github.com/craue/CraueFormFlowBundle/issues/87

## 2.1.0 (2013-08-27)

- [#75]: the hidden step field is automatically added to the form (follow `UPGRADE-2.1.md` to upgrade)

[#75]: https://github.com/craue/CraueFormFlowBundle/issues/75

## 2.0.1 (2013-07-12)

- fixed steps being incorrectly marked as skipped when resetting the flow

## 2.0.0 (2013-05-27)

- BC breaks (follow `UPGRADE-2.0.md` to upgrade):
  - [#46]: reworked the way steps are defined
  - adjustments in handling the request for Symfony 2.3 compatibility
- [#52]: added `GetStepsEvent`
- added `PostBindFlowEvent`

[#46]: https://github.com/craue/CraueFormFlowBundle/issues/46
[#52]: https://github.com/craue/CraueFormFlowBundle/issues/52

## 1.1.3 (2013-05-23)

- [#48]: added method `getStorage`
- made the dependency on an event dispatcher optional

[#48]: https://github.com/craue/CraueFormFlowBundle/issues/48

## 1.1.2 (2013-04-17)

- always dispatch `PreBindEvent` when `bind` is called (to match expected behavior)
- [#45]: added Brazilian Portuguese translation

[#45]: https://github.com/craue/CraueFormFlowBundle/issues/45

## 1.1.1 (2013-04-14)

- avoid skipping all steps by tampering with the hidden step field
- added some basic tests

## 1.1.0 (2013-02-28)

- adjustments to changes in the Form component for Symfony 2.1.*
- adjustments to changes in the HttpFoundation component for Symfony 2.1.*
- [#21]: added `StorageInterface`
- [#23]: added route parameters to links generated for dynamic step navigation
- preserve given `validation_groups` option
- added the flow instance as a property in events
- throw an exception if the number of steps doesn't match the number of step descriptions

[#21]: https://github.com/craue/CraueFormFlowBundle/issues/21
[#23]: https://github.com/craue/CraueFormFlowBundle/issues/23

## 1.0.0 (2012-03-07)

- first stable release for Symfony 2.0.*

## 2011-08-02

- initial commit
