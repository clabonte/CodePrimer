# CodePrimer
![build-shield]
[![coverage-shield]][coverage-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

**CodePrimer**’s primary objective is to **allow software architects and senior developers to define the data model and related processing from a business point of view** so that it can be easily understood and manipulated by entrepreneurs, business managers and business analysts before starting the software development process. 

As such, it is meant to **prime** the software development process using simple, easy to use and effective representations that can be objectively interpreted by business and techies willing to cooperate to create digital businesses.

**CodePrimer** interprets this data model to **generate a set of artifacts (e.g. source code)** that are combined to **build a production-grade software solution following the architect's vision** to guide the development team to use his best practices.
 
The project offers an opinionated architecture, called [Business Bundle](doc/bundle/Overview.md) to get started. The resulting solution is meant to be highly scalable, yet requiring very low maintenance by leveraging the best DevOps practices with a minimal technical team.

It liberally borrows and integrates concepts and best practices that have been made available over the years by several movements in the software development space, such as UML, Domain-Driven Design (DDD), agile, micro services and DevOps. 

The resulting architecture and implementation is not meant to please everyone but the underlying goal is to produce top quality software that is meeting the business objectives for what it is being built and be easily understood, maintained and evolved by a software development team at a fraction of the time usually required to achieve production-grade software.. 

Thanks to the **high flexibility** of its artifact generation engine, the project can be easily tailored to suit any programming language or any architecture/framework chosen by the architect. 

## Documentation
In order to fully understand and leverage the capabilities offered by **CodePrimer**, you should refer to the following documents:
- [Concepts](doc/Concepts.md)
- [Structure and Guidelines](doc/StructureAndGuidelines.md)
- [CodePrimer Data Modeling](doc/DataModel.md)
- [CodePrimer Process Modeling](doc/bundle/Overview.md)

## Getting Started
To get a local copy up and running follow these simple steps.

### Prerequisites
The installation instructions assume you already have the following:
- [PHP 7.3](https://www.php.net/manual/en/install.php) or above
- [composer](https://getcomposer.org/)

### Installation
1. Clone the repo
2. Run composer install
 
```sh
git clone https://github.com/clabonte/codeprimer.git
cd codeprimer
composer install
```

## Usage
In order to understand how to use CodePrimer, it is best to [look at a sample application](doc/sample/Index.md)

## Roadmap
The high level roadmap and progress is available [here](https://github.com/clabonte/codeprimer/milestones?with_issues=no)

To have a more detailed view on upcoming changes, please look at the [Product Roadmap][roadmap-url] or [open issues][issues-url] for a list of proposed features and known issues. 

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md)

## Contact
Christian Labonte [![LinkedIn][linkedin-shield]][linkedin-url]

Project Link: https://github.com/clabonte/codeprimer

## License
This project uses the following license: [MIT][license-url]


<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[build-shield]: https://img.shields.io/github/workflow/status/clabonte/codeprimer/Validate%20Master/master
[coverage-shield]: https://img.shields.io/codecov/c/github/clabonte/codeprimer
[coverage-url]: https://codecov.io/gh/clabonte/codeprimer
[issues-shield]: https://img.shields.io/github/issues/clabonte/codeprimer
[issues-url]: https://github.com/clabonte/codeprimer/issues
[ideas-url]: https://github.com/clabonte/codeprimer/labels/idea
[roadmap-url]: https://github.com/clabonte/codeprimer/projects/1
[license-shield]: https://img.shields.io/badge/License-MIT-yellow.svg
[license-url]: https://github.com/clabonte/codeprimer/blob/master/LICENSE
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-blue.svg?logo=linkedin
[linkedin-url]: https://www.linkedin.com/in/christianlabonte/
