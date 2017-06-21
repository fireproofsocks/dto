# Contributing

Contributions are **welcome** and will be fully **credited**.

Contributions are accepted via Pull Requests on [Github](https://github.com/fireproofsocks/dto/pulls).

## Bugs

Thank you for taking the time to file a bug report!  Every bug report needs to provide the exact steps to replicate a 
problem AND it needs to clarify what was the _expected_ result compared to what was the _expected_ result.  Please include
your sample code and err on the side of being too verbose.  Thanks!

## Features

If there's a feature you would like to see implemented, please describe it in detail and include some examples of the desired behavior. 
Pseudo-code is welcome.

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests to verify the behavior (this packages strives for 100% test coverage).

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

### Creating a Fork

- Sign up for a Github account if you don't have one already.

- Fork the repo in your Github dashboard

- Clone your fork of the repo (e.g. `https://github.com/your-user/dto`)

- Create a new branch for the particular feature or bug you are working on: `git checkout -b feature/123/some-new-feature`

#### Branch Names

The naming convention used for branches is `{issue-type}/{issue-number}/{snake-case-description}` 
It aims to be as self-explanatory as possible.  Here's the short breakdown of the name components:

##### Issue Type:

- `feature` the branch is devoted to implementing a new feature
- `bug` : denotes that the branch is focused on fixing a bug
- `hotfix` : reserved for urgent fixes 

##### Issue Number

This is a simple way to ear-mark the branch name with the issue-number in the [Github Issue Tracker for this repository](https://github.com/fireproofsocks/dto/issues)

##### Snake Case Description

This should be a short description (literally only a handful of words) that can remind you (the developer) of what that 
branch was for.  This is especially useful if you happen to have multiple branches open.

### Squashing Commit History

Following proper Git protocol to make life easier for colleagues and code-reviewers can be a bit of a mystery to newcomers.  
Each commit on a Pull Request should contain a logical and manageable change that can be reviewed independently.  For 
many of us, we get so focused on implementing a feature or fixing a bug that we don't give much thought to all the commits
that may have happened during the process of getting there.  Maybe we committed changes before stepping out for lunch, or
maybe we needed to change branches.  Maybe you're thinking of Git as a backup tool, but this isn't ideal when someone 
needs to review your code.  As much as possible, try to group changes into smallish, manageable commits.

If you have more commits than you need, you can squash them by editing your Git history.

- https://ariejan.net/2011/07/05/git-squash-your-latests-commits-into-one/

## Running Tests

All tests are written in [PHPUnit](https://phpunit.de/).  All tests can be run executing the `phpunit` executable:
 
``` bash
$ phpunit
```

Or if it is not installed globally, you can run the dev version of `phpunit` installed via composer:

``` bash
$ vendor/phpunit/phpunit/phpunit
```

**Happy coding**!