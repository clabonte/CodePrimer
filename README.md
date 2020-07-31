# CodePrimer
![build-shield]
[![coverage-shield]][coverage-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

**CodePrimer**’s primary objective is to **allow software architects, business analysts and senior developers to define the data model and related processing from a business point of view** so that it can be easily understood and manipulated by entrepreneurs and business managers before starting the software development process. 

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
Contributions are what make the open source community such an amazing place to be learn, inspire, and create. 

Any contributions you make are **greatly appreciated.** 

Here are **some ways you can help**:

1. Pick one of the [issues][issues-url] currently opened.
2. Select items in the [Project Roadmap][roadmap-url].
3. Submit your [ideas][ideas-url].
4. Add support for other programming languages by creating the appropriate [Twig template](https://twig.symfony.com/)

To contribute to **CodePrimer**, follow these steps:

1. Fork this repository
2. Create your feature branch `git checkout -b feature/AmazingFeature`
3. Make your changes and commit them `git commit -m 'Add some AmazingFeature'`
4. Push to the original branch `git push origin feature/AmazingFeature`
5. Create the Pull Request against the master branch on the `clabonte/codeprimer` repository

If you are interested to contribute on a frequent basis, feel free to contact me so I can add you as a repository member.

In order to maintain the quality of this project, **important rules** must be followed for Pull Requests to be considered:

1. Make sure all test cases are passing by running the following command: `composer phpunit`
2. Make sure your code is following the project's coding standards by running the following command: `composer php-cs-fixer`
3. Make sure you maintain the project's code coverage above 90% (ideally 95%)

The **above rules are being strictly enforced** via GitHub workflows for any Pull Request opened against the master branch. 

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
