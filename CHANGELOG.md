# [4.2.0](https://github.com/secretary/php/compare/4.1.1...4.2.0) (2026-01-21)


### Features

* **ci:** add workflow_dispatch trigger to split workflow ([092df80](https://github.com/secretary/php/commit/092df80acbdb51f9801a916a0102866b37affb84))

## [4.1.1](https://github.com/secretary/php/compare/4.1.0...4.1.1) (2026-01-16)


### Bug Fixes

* exclude PHP 8.5 prefer-lowest from static analysis ([5dc4d3e](https://github.com/secretary/php/commit/5dc4d3eb96523fecc8343778c7acb8ae3cd552a4))
* require Mockery ^1.6.12 for PHP 8.4 compatibility ([8c39289](https://github.com/secretary/php/commit/8c392896a7c182d10ae230b7dc7db3327e9eb830))
* require Psalm ^5.16 for MissingOverrideAttribute support ([c29c5d1](https://github.com/secretary/php/commit/c29c5d1f53806cce5b8ebd4dc74932f506eeddf2))
* require Psalm ^5.26 for MissingOverrideAttribute support ([be7c17b](https://github.com/secretary/php/commit/be7c17b5877d697f4873e6d5b67e8d9dd3e33f70))
* require Psalm ^6.14.3 and disable fail-fast in CI ([c5d5c0b](https://github.com/secretary/php/commit/c5d5c0bf36e064b8145f2ce08e956dd5218817b6))
* update dependencies for PHP 8.2-8.5 compatibility ([1c2db3d](https://github.com/secretary/php/commit/1c2db3d4607dafcab78a3490b2b5f9ffecf5646d))

# [4.1.0](https://github.com/secretary/php/compare/4.0.0...4.1.0) (2026-01-16)


### Features

* add GCP Secrets Manager adapter and modernize codebase ([198cf12](https://github.com/secretary/php/commit/198cf127e28d962d16e56252b35f22f4db719ded))

# [4.0.0](https://github.com/secretary/php/compare/3.1.1...4.0.0) (2026-01-16)


* feat!: require PHP 8.2 or higher ([dd8d74c](https://github.com/secretary/php/commit/dd8d74c51df8626d091f37cfb10f524deff3ee5d))


### BREAKING CHANGES

* Drop support for PHP 8.0 and 8.1.
All packages now require PHP ^8.2.

## [3.1.1](https://github.com/secretary/php/compare/3.1.0...3.1.1) (2026-01-15)


### Bug Fixes

* use ^3.0 for secretary/core in GCP adapter ([683a32c](https://github.com/secretary/php/commit/683a32ccd5d9119fd792dd4ed4361c4733cfa5a8))

# [3.1.0](https://github.com/secretary/php/compare/3.0.6...3.1.0) (2026-01-15)


### Features

* add GCP Secrets Manager adapter ([3b2fe5c](https://github.com/secretary/php/commit/3b2fe5cf82ccfd075c26ecf755e769fc4cfd1576))

## [3.0.6](https://github.com/secretary/php/compare/3.0.5...3.0.6) (2025-02-05)


### Bug Fixes

* Fixed Symfony deprecation ([03f1da5](https://github.com/secretary/php/commit/03f1da511717ba3dc8cbb70faf75772218ed6373))

## [3.0.5](https://github.com/secretary/php/compare/3.0.4...3.0.5) (2025-02-05)


### Bug Fixes

* Fixed PHP 8.4 deprecation ([19e99e7](https://github.com/secretary/php/commit/19e99e7a7c6f37cdbf33fef887c7d11ba44cfef9))

## [3.0.4](https://github.com/secretary/php/compare/3.0.3...3.0.4) (2024-01-29)


### Bug Fixes

* ArrayHelper remove method ([61d1477](https://github.com/secretary/php/commit/61d147755c1091bbf9b08b6792debd041a0286fd))

## [3.0.3](https://github.com/secretary/php/compare/3.0.2...3.0.3) (2024-01-15)


### Bug Fixes

* Optimized array helper method ([f996d0b](https://github.com/secretary/php/commit/f996d0b6fd9e524787535bb3683fb884450d5d3e))

## [3.0.2](https://github.com/secretary/php/compare/3.0.1...3.0.2) (2024-01-11)


### Bug Fixes

* Array helper ([4098f7e](https://github.com/secretary/php/commit/4098f7e4d578fefb71a70739976783d72e4a238f))

## [3.0.1](https://github.com/secretary/php/compare/3.0.0...3.0.1) (2024-01-10)


### Bug Fixes

* **CI:** Updated node and semantic-release ([096e283](https://github.com/secretary/php/commit/096e283ce38a77ba0901746612f2be1320867900))

## [2.1.1](https://github.com/secretary/php/compare/2.1.0...2.1.1) (2024-01-09)


### Bug Fixes

* Allow PHP Unit 10 ([f20907e](https://github.com/secretary/php/commit/f20907efcf2b84c3c07b1d2e01747e54ecfb42e5))

## [2.0.1](https://github.com/secretary/php/compare/2.0.0...2.0.1) (2024-01-09)


### Bug Fixes

* **global:** Allow Symfony 7 ([#17](https://github.com/secretary/php/issues/17)) ([86ef910](https://github.com/secretary/php/commit/86ef9100d7f233f0c57df148a0ee24886e6eefbd))


### Performance Improvements

* **Symfony:** Allow SF 7 ([ac9146c](https://github.com/secretary/php/commit/ac9146c583a93a8706aaea7b770ab83ee510536c))

# [2.0.0](https://github.com/secretary/php/compare/1.4.0...2.0.0) (2022-02-12)


### Performance Improvements

* **PHP:** Dropped support for PHP 7.4 ([b14daf1](https://github.com/secretary/php/commit/b14daf1822875fa2c3f7cb2f8840737df7fed1bc))


### BREAKING CHANGES

* **PHP:** Removed support for PHP 7.4

# [1.4.0](https://github.com/secretary/php/compare/1.3.2...1.4.0) (2022-02-11)


### Bug Fixes

* **CI:** Replaced Travis CI with GH workflow ([0c9fdb3](https://github.com/secretary/php/commit/0c9fdb3b3110b8060be96a06d0766bff4549bca6))
* **CI:** Replaced Travis CI with GH workflow ([a88154d](https://github.com/secretary/php/commit/a88154d88c8976d2990f6c62d1cbd8d0394c62ca))


### Features

* **global:** PHP 7.4 as minimum PHP version ([eb2ff91](https://github.com/secretary/php/commit/eb2ff91db84c3924eab57f43d59aa5c4eac61e4d))

## [1.3.2](https://github.com/secretary/php/compare/1.3.1...1.3.2) (2021-02-28)


### Bug Fixes

* **chore:** Triggering release ([7d43834](https://github.com/secretary/php/commit/7d4383441f058377bca4258408d38e05eb81853a))

## [1.3.1](https://github.com/secretary/php/compare/1.3.0...1.3.1) (2021-02-23)


### Bug Fixes

* **chore:** Triggering release ([74123e6](https://github.com/secretary/php/commit/74123e632d1c182a4d03ebf6852ca2e7a6dade1a))

# [1.3.0](https://github.com/secretary/php/compare/1.2.18...1.3.0) (2021-02-14)


### Bug Fixes

* **travis:** tests for php 8 ([9889dbf](https://github.com/secretary/php/commit/9889dbfd9fa639d6a942c2b2577323beccadb923))
* **travis:** tests for php 8 ([4ddfeaa](https://github.com/secretary/php/commit/4ddfeaaf27b8558a49d1a9381c2df335dbc0a96f))
* **travis:** tests for php 8 ([425918e](https://github.com/secretary/php/commit/425918e173f99ce623ab96ac2993675788d18d51))


### Features

* **global:** PHP 8 Support ([27be33d](https://github.com/secretary/php/commit/27be33d2771b56e938d38e335c442a19c3427c74))

## [1.2.18](https://github.com/secretary/php/compare/1.2.17...1.2.18) (2019-08-15)


### Bug Fixes

* **bundle:** Fixing cache adapters ([e6fd44d](https://github.com/secretary/php/commit/e6fd44d))
* **bundle:** Fixing cache adapters ([97504a2](https://github.com/secretary/php/commit/97504a2))

## [1.2.17](https://github.com/secretary/php/compare/1.2.16...1.2.17) (2019-08-15)


### Bug Fixes

* **aws:** Throwing error when the value for a secret can't be found ([78baf8c](https://github.com/secretary/php/commit/78baf8c))

## [1.2.16](https://github.com/secretary/php/compare/1.2.15...1.2.16) (2019-08-15)


### Bug Fixes

* **aws:** Throwing error when the value for a secret can't be found ([9202f85](https://github.com/secretary/php/commit/9202f85))

## [1.2.15](https://github.com/secretary/php/compare/1.2.14...1.2.15) (2019-08-14)


### Bug Fixes

* **aws:** Throwing error when the value for a secret can't be found ([38c7818](https://github.com/secretary/php/commit/38c7818))

## [1.2.14](https://github.com/secretary/php/compare/1.2.13...1.2.14) (2019-08-14)


### Bug Fixes

* **aws:** Throwing error when the value for a secret can't be found ([e99e036](https://github.com/secretary/php/commit/e99e036))

## [1.2.13](https://github.com/secretary/php/compare/1.2.12...1.2.13) (2019-08-14)


### Bug Fixes

* **bundle:** Fixing runtime error in key-not-found scenario ([92b1694](https://github.com/secretary/php/commit/92b1694))
* **bundle:** Fixing runtime error in key-not-found scenario ([d698cf5](https://github.com/secretary/php/commit/d698cf5))

## [1.2.12](https://github.com/secretary/php/compare/1.2.11...1.2.12) (2019-08-14)


### Bug Fixes

* **chain:** Fixing chain adapter ([6449bb4](https://github.com/secretary/php/commit/6449bb4))

## [1.2.11](https://github.com/secretary/php/compare/1.2.10...1.2.11) (2019-08-13)


### Bug Fixes

* **bundle:** Making adapters public ([3b5f4ef](https://github.com/secretary/php/commit/3b5f4ef))

## [1.2.10](https://github.com/secretary/php/compare/1.2.9...1.2.10) (2019-08-13)


### Bug Fixes

* **chain:** Fixing bad package name ([074eb9c](https://github.com/secretary/php/commit/074eb9c))

## [1.2.9](https://github.com/secretary/php/compare/1.2.8...1.2.9) (2019-08-13)


### Bug Fixes

* **json:** Check metadata ([#4](https://github.com/secretary/php/issues/4)) ([80ef20e](https://github.com/secretary/php/commit/80ef20e))

## [1.2.8](https://github.com/secretary/php/compare/1.2.7...1.2.8) (2019-08-13)


### Bug Fixes

* **travis:** Fixing flow ([39284e8](https://github.com/secretary/php/commit/39284e8))
