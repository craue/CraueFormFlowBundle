# Changelog

## master

- BC breaks (follow `UPGRADE-3.0.md` to upgrade):
  - [#101]: support for concurrent instances of the same flow
  - [#104]: removed options from method `createForm`
  - [#145]: bumped Symfony dependency to 2.3
  - [#148]: restructured data storage
  - [#180]: renamed step config option `type` to `form_type`
  - [#184]: made the bundle Symfony 3 compatible, bumped Symfony dependency to 2.6
  - [#222]: bumped Symfony dependency to 2.7
  - removed the step field template
  - renamed property `step` to `stepNumber` and method `getStep` to `getStepNumber` within event classes
- [#98]+[#143]: add a validation error to the current form if a form of a previous step became invalid
- [#107]: added Czech translation
- [#112]: improved Dutch translation
- [#117]: method `getFormOptions` returns an array for the `validation_groups` option
- [#122]: added support for PUT method
- [#125]: added generic form options to simplify passing options to all steps
- [#126]: allow custom classes on buttons
- [#133]+[#134]: added Farsi translation
- [#142]: added support for the "redirect after submit" pattern
- [#146]: handling of file uploads
- [#175]+[#178]: form options for each step
- [#196]: allow to use a closure for the `validation_groups` option
- [#215]: added a default `getName` implementation
- [#217]: added DoctrineStorage to store data in a Doctrine-managed database
- [#219]+[#238]: allow a callable for each step label
- [#220]: added expired form detection
- [#226]: allow custom button labels
- [#254]: added Slovak translation
- [#262]: added getters for steps remaining and done
- [#280]: added an option for removing the reset button

[#98]: https://github.com/craue/CraueFormFlowBundle/issues/98
[#101]: https://github.com/craue/CraueFormFlowBundle/issues/101
[#104]: https://github.com/craue/CraueFormFlowBundle/issues/104
[#107]: https://github.com/craue/CraueFormFlowBundle/issues/107
[#112]: https://github.com/craue/CraueFormFlowBundle/issues/112
[#117]: https://github.com/craue/CraueFormFlowBundle/issues/117
[#122]: https://github.com/craue/CraueFormFlowBundle/issues/122
[#125]: https://github.com/craue/CraueFormFlowBundle/issues/125
[#126]: https://github.com/craue/CraueFormFlowBundle/issues/126
[#133]: https://github.com/craue/CraueFormFlowBundle/issues/133
[#134]: https://github.com/craue/CraueFormFlowBundle/issues/134
[#142]: https://github.com/craue/CraueFormFlowBundle/issues/142
[#143]: https://github.com/craue/CraueFormFlowBundle/issues/143
[#145]: https://github.com/craue/CraueFormFlowBundle/issues/145
[#146]: https://github.com/craue/CraueFormFlowBundle/issues/146
[#148]: https://github.com/craue/CraueFormFlowBundle/issues/148
[#175]: https://github.com/craue/CraueFormFlowBundle/issues/175
[#178]: https://github.com/craue/CraueFormFlowBundle/issues/178
[#180]: https://github.com/craue/CraueFormFlowBundle/issues/180
[#184]: https://github.com/craue/CraueFormFlowBundle/issues/184
[#196]: https://github.com/craue/CraueFormFlowBundle/issues/196
[#215]: https://github.com/craue/CraueFormFlowBundle/issues/215
[#217]: https://github.com/craue/CraueFormFlowBundle/issues/217
[#219]: https://github.com/craue/CraueFormFlowBundle/issues/219
[#220]: https://github.com/craue/CraueFormFlowBundle/issues/220
[#222]: https://github.com/craue/CraueFormFlowBundle/issues/222
[#226]: https://github.com/craue/CraueFormFlowBundle/issues/226
[#238]: https://github.com/craue/CraueFormFlowBundle/issues/238
[#254]: https://github.com/craue/CraueFormFlowBundle/issues/254
[#262]: https://github.com/craue/CraueFormFlowBundle/issues/262
[#280]: https://github.com/craue/CraueFormFlowBundle/issues/280

## [2.1.9] – 2015-12-29

- [#205]: added conditional code updates to avoid most deprecation notices with Symfony 2.8
- added forward compatibility for Twig 2.0
- added support for PHP 7.0 and HHVM

[#205]: https://github.com/craue/CraueFormFlowBundle/issues/205
[2.1.9]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.8...2.1.9

## [2.1.8] – 2015-06-11

- [#169]: simplified some code meant for avoiding deprecation notices
- suppress errors when triggering deprecation notices

[#169]: https://github.com/craue/CraueFormFlowBundle/issues/169
[2.1.8]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.7...2.1.8

## [2.1.7] – 2015-03-06

- avoid bubbling up of a possible OutOfBoundsException while determining the current step number
- fixed minimum version of Symfony

[2.1.7]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.6...2.1.7

## [2.1.6] – 2015-02-02

- added conditional code updates to avoid deprecation notices

[2.1.6]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.5...2.1.6

## [2.1.5] – 2014-06-13

- [#132]: fixed BC method `hasSkipStep`

[#132]: https://github.com/craue/CraueFormFlowBundle/issues/132
[2.1.5]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.4...2.1.5

## [2.1.4] – 2013-12-05

- adjusted Composer constraint to allow being used with SensioFrameworkExtraBundle 3.0

[2.1.4]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.3...2.1.4

## [2.1.3] – 2013-11-18

- [#94]: disallow invalid step config options
- ensure that `Step#isSkipped` always returns a boolean value
- avoid triggering deprecation errors when used with Symfony 2.1.x
- [#100]: fixed the step list to avoid linking not yet accessible steps

[#94]: https://github.com/craue/CraueFormFlowBundle/issues/94
[#100]: https://github.com/craue/CraueFormFlowBundle/issues/100
[2.1.3]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.2...2.1.3

## [2.1.2] – 2013-09-26

- [#90]: fixed the step list to render the last step already been visited (but not submitted) as a link

[#90]: https://github.com/craue/CraueFormFlowBundle/issues/90
[2.1.2]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.1...2.1.2

## [2.1.1] – 2013-09-24

- ensure that `skip` callables always return a boolean value
- [#87]: the step parameter used in links to specific steps is not limited to be a query parameter anymore, e.g. can be a route parameter

[#87]: https://github.com/craue/CraueFormFlowBundle/issues/87
[2.1.1]: https://github.com/craue/CraueFormFlowBundle/compare/2.1.0...2.1.1

## [2.1.0] – 2013-08-27

- [#75]: the hidden step field is automatically added to the form (follow `UPGRADE-2.1.md` to upgrade)

[#75]: https://github.com/craue/CraueFormFlowBundle/issues/75
[2.1.0]: https://github.com/craue/CraueFormFlowBundle/compare/2.0.1...2.1.0

## [2.0.1] – 2013-07-12

- fixed steps being incorrectly marked as skipped when resetting the flow
[2.0.1]: https://github.com/craue/CraueFormFlowBundle/compare/2.0.0...2.0.1

## [2.0.0] – 2013-05-27

- BC breaks (follow `UPGRADE-2.0.md` to upgrade):
  - [#46]: reworked the way steps are defined
  - adjustments in handling the request for Symfony 2.3 compatibility
- [#52]: added `GetStepsEvent`
- added `PostBindFlowEvent`

[#46]: https://github.com/craue/CraueFormFlowBundle/issues/46
[#52]: https://github.com/craue/CraueFormFlowBundle/issues/52
[2.0.0]: https://github.com/craue/CraueFormFlowBundle/compare/1.1.3...2.0.0

## [1.1.3] – 2013-05-23

- [#48]: added method `getStorage`
- made the dependency on an event dispatcher optional

[#48]: https://github.com/craue/CraueFormFlowBundle/issues/48
[1.1.3]: https://github.com/craue/CraueFormFlowBundle/compare/1.1.2...1.1.3

## [1.1.2] – 2013-04-17

- always dispatch `PreBindEvent` when `bind` is called (to match expected behavior)
- [#45]: added Brazilian Portuguese translation

[#45]: https://github.com/craue/CraueFormFlowBundle/issues/45
[1.1.2]: https://github.com/craue/CraueFormFlowBundle/compare/1.1.1...1.1.2

## [1.1.1] – 2013-04-14

- avoid skipping all steps by tampering with the hidden step field
- added some basic tests

[1.1.1]: https://github.com/craue/CraueFormFlowBundle/compare/1.1.0...1.1.1

## [1.1.0] – 2013-02-28

- adjustments to changes in the Form component for Symfony 2.1.*
- adjustments to changes in the HttpFoundation component for Symfony 2.1.*
- [#21]: added `StorageInterface`
- [#23]: added route parameters to links generated for dynamic step navigation
- preserve given `validation_groups` option
- added the flow instance as a property in events
- throw an exception if the number of steps doesn't match the number of step descriptions

[#21]: https://github.com/craue/CraueFormFlowBundle/issues/21
[#23]: https://github.com/craue/CraueFormFlowBundle/issues/23
[1.1.0]: https://github.com/craue/CraueFormFlowBundle/compare/1.0.0...1.1.0

## 1.0.0 – 2012-03-07

- first stable release for Symfony 2.0.*

## 2011-08-02

- initial commit
