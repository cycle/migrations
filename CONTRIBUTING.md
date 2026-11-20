# Contributing
Feel free to contribute to the development of the Cycle Database Migrations.
Please make sure that the following requirements are satisfied before submitting your pull request:

* KISS
* PSR-12
* `declare(strict_types=1);` is mandatory
* Your code must include tests

> Use our discord server to check for the advice or suggestion https://discord.gg/FZ9BCWg

## Testing
Tests run on [Testo](https://github.com/php-testo/testo) through the `cycle/testing` harness: `tests/Unit` needs no
database, `tests/Feature` runs once per database driver, and reproductions of reported issues go to
`tests/Regression/Issue<N>/`. Generated migration files land in `tests/files/` and are removed after each test.

Unit and SQLite tests need no docker:

```bash
composer test:unit
composer test:sqlite
```

The other drivers run against the shared test databases:

```bash
composer db:up              # or: composer db:up -- mysql
composer test               # every suite; drivers whose server is down are skipped
composer test:mysql         # also test:postgres, test:sqlserver
composer db:down
```

## Help Needed In
If you want to help but don't know where to start:

* TODOs
* Updating to latest dev-dependencies (Testo, Mockery, etc)
* Quality recommendations and improvements
* Check [Open Issues](https://github.com/cycle/migrations/issues)
* More tests are always welcome
* Typos

Feel free to propose any ideas related to architecture, docs (___docs are never complete___),  adaptation or community.

> Original guide author is not a native English speaker, feel free to create PR for any text corrections.

## Critical/Security Issues
If you found something which shouldn't be there or a bug which opens a security hole please let me know immediately by email
[team@spiralscout.com](mailto:team@spiralscout.com)

## Official Support
Cycle ORM and all related components are maintained by [Spiral Scout](https://spiralscout.com/).

For commercial support please contact team@spiralscout.com.

## Licensing
Cycle Database Migrations will remain under [MIT license](/LICENSE) indefinitely.
