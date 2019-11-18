# Traya

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Azure Pipelines](https://img.shields.io/azure-devops/build/pontoreausylvain/75e3bd3f-37cc-4383-9c61-c82efc276085/3)](https://dev.azure.com/pontoreausylvain/Traya/_build?definitionId=3)
![Azure DevOps tests](https://img.shields.io/azure-devops/tests/pontoreausylvain/traya/3)

**Traya** is an AggregateRoot & Event Publishing component built by [Wynd](https://www.wynd.eu) and used by our backend projects.

This project is part of the _Sith Triumvirate_ project with:
- [Nihilus](https://github.com/Wynd-Lab/nihilus): CQRS
- Sion: Saga pattern (WIP, not available yet)

Traya requires a specific implementation if you want to publish events (this part is optionnal). Wynd doesn't provide any default implementation because the event management realy depends on the technology/framework you're using (Prooph, Event Store, Kafka, RabbitMQ, ...). More information are available in the documentation.

## Quick start

[Overview](/doc/README.md) and [examples](/examples)

## Contribution

For any contribution (Issue and PR), please follow project [guidelines](CONTRIBUTING.md). 

Add a ⭐️ is also a way to contribute to this repository 😉

This project has adopted the code of conduct defined by the [Contributor Covenant](https://www.contributor-covenant.org/). Before contributing to this repository, take a look at the [code of conduct](CODE_OF_CONDUCT.md).

## Licence

[MIT](LICENSE)
